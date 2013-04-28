<?php

	/* 
	Author: Iulia Maria Comsa
	Email:  iulia.comsa1@gmail.com
	*/
	
	/* FUNCTION: subsample $nr_sample subgraphs of size $subgraph_size */
	function subsample($subgraph_size, $nr_samples)
	{
		global $nr_nodes;	/* the nr of nodes of the graph */
		global $nr_edges;	/* the nr of edges of the graph */
		global $graph;		/* the graph as a list of nodes/neighburs/edges, i.e. graph[node1][node2] = array of edges between node1 and node2 */
		global $graph_invers;	/* the inverted graph in the same format, i.e. graph[node1][node2] = array of edges between node2 and node1 */
		global $graph_edgelist;	/* the graph as an array of (node1 node2 edge) */
		
		echo("Subgraph size is " . $subgraph_size . ".<br/>Sampling " . $nr_samples . " subgraphs.<br/>");
		
		echo("Generating permutations from 1 to " . $subgraph_size . "...<br><br>");
		$items = array();
		for($i = 1; $i <= $subgraph_size; ++$i) $items[] = $i;		/* make an array for indices of nodes in the subgraph */
		
		$permutations = array();
		pc_permute($items,$permutations);
		
		$counter = 0;
		$subgraphs = array();	/* keeps the found subgraphs */
		$subgraphs_count = array();			/* nr of subgraphs */
		$subgraphs_score = array();			/* subgraph score (see paper by N.Kashtan et al.) */
		$subgraphs_vertices = array();		/* actual subgraphs in the graph with that form */
		$total_score = 0;					/* sum of scores, kept for normalizing at the end */
		
		while($counter < $nr_samples)		/* for $nr_samples times... */
		{
			$subgraph = array();	/* a subgraph is represented as a matrix, where element [i][j] contains the edges from node i to j */
			/*for($i = 1; $i <= $nr_nodes; ++$i)
				for($j = 1; $j <= $nr_nodes; ++$j)
					$subgraph[$i][$j] = array();*/
								
			
			$first_edge = $graph_edgelist[array_rand($graph_edgelist)];		/* select a random edge */
			$E = array(); $E[] = $first_edge;								/* an array of edges we can use (linked to current nodes) */
			$U = array();					/* an array of used edges */
			$V = array();					/* an array of selected nodes */
			$V2 = array();					/* a mirror of V */
			
			$P = 1.0/$nr_edges;				/* the probability of the selecting the current subgraph */
			
			while(1)
			{
				/* select an edge */
				$crt_edge_index = array_rand($E);	/* select index */
				$crt_edge = $E[$crt_edge_index];	/* select edge with that index */
				$crt_P = 1.0 / count($E);			/* probability of selecting this edge */
				$node1 = $crt_edge[0];				/* source node */
				$node2 = $crt_edge[1];				/* dest node */
				$edge = $crt_edge[2];				/* edge value */
				unset($E[$crt_edge_index]);			/* remove edge from E */
				$U[] = $crt_edge;					/* add edge to U (used edges) */
				
				$V2 = $V; 
				
				/* add nodes to V & edges, if not already there */
				if(!in_array($node1, $V)) 
				{ 
					$V[] = $node1;
					if(is_array($graph[$node1])) foreach ($graph[$node1] as $temp_node2 => $temp_edges) 
						foreach($temp_edges as $temp_edgeval)
						{	
							$temp_edge = array($node1,$temp_node2,$temp_edgeval);
							if(!in_array($temp_edge,$E) && !in_array($temp_edge,$U))
								$E[] = $temp_edge;
						}
					if(is_array($graph_invers[$node1])) foreach ($graph_invers[$node1] as $temp_node2 => $temp_edges) 
						foreach($temp_edges as $temp_edgeval)
						{	
							$temp_edge = array($temp_node2,$node1,$temp_edgeval);
							if(!in_array($temp_edge,$E) && !in_array($temp_edge,$U))
								$E[] = $temp_edge;
						}
				}	
				if(!in_array($node2, $V)) 
				{ 
					$V[] = $node2;
					if(is_array($graph[$node2])) foreach ($graph[$node2] as $temp_node2 => $temp_edges) 
						foreach($temp_edges as $temp_edgeval)
						{	
							$temp_edge = array($node2,$temp_node2,$temp_edgeval);
							if(!in_array($temp_edge,$E) && !in_array($temp_edge,$U))
								$E[] = $temp_edge;
						}
					if(is_array($graph_invers[$node2])) foreach ($graph_invers[$node2] as $temp_node2 => $temp_edges) 
						foreach($temp_edges as $temp_edgeval)
						{	
							$temp_edge = array($temp_node2,$node2,$temp_edgeval);
							if(!in_array($temp_edge,$E) && !in_array($temp_edge,$U))
								$E[] = $temp_edge;
						}
				}
				
				/* check for stop condition */
				if(count($V) > $subgraph_size) break;	/* yes, yes, I know this does half an extra interation, but whatever */
				
				/* update probability */
				$P = $P * $crt_P;
				
				/* update subgraph representation */
				$subgraph[$node1][$node2][] = $edge;			
			}	
			
			$V = $V2;
			sort($V);
			//echo("<br><br>selected graph with probability " . $P . "<br>");
			$score = 1.0/$P;
			$s = 0;
			$found = 0;
			while($s < count($subgraphs))	/* for all subgraphs that we already have found */
			{
				foreach($permutations as $perm)	/* for each permutation */
				{
					/* make the map from the subgraph to the current V */
					$map_S_V = array();
					$map_V_S = array();
					for($i = 0; $i < $subgraph_size; ++$i) { $map_S_V[$perm[$i]] = $V[$i]; $map_V_S[$V[$i]] = $perm[$i]; }
					if(onesided_equal_graphs($subgraph, $subgraphs[$s], $map_V_S) && onesided_equal_graphs($subgraphs[$s], $subgraph, $map_S_V)) /* if the curent subgraph is identical for this node labeling (permutation) */
					{
						$subgraphs_count[$s]++;	/* increase the count for that subgraph and quit loop */
						$subgraphs_score[$s] += $score;	/* update score for that subgraph */
						$total_score += $score;
						if(!in_array($V,$subgraphs_vertices[$s])) $subgraphs_vertices[$s][] = $V;
						$found = 1;
						break;
					}
				}
				if($found) break;
				++$s;
			}
			 
			if($s >= count($subgraphs))		/* if no subgraph is identical for some node labeling */
			{
				/* then add this subgraph using a node labeling from 1 to subgraph_size */
				$perm = $permutations[0]; /* just use the first permutation for labeling  */
				$new_subgr = array();
				foreach($subgraph as $node1 => $node2_list)
				{
					$new_node1 = $perm[array_search($node1,$V)];
					foreach($node2_list as $node2 => $edges)
					{
						$new_node2 = $perm[array_search($node2,$V)];
						$new_subgr[$new_node1][$new_node2] = $edges;
					}
				}
				
				$subgraphs[] = $new_subgr;		/* then add this subgraph */
				$subgraphs_count[] = 1;			/* with an initial one appearence */
				$subgraphs_score[] = $score;			/* and an initial score */
				$subgraphs_vertices[][] = $V;
				$total_score += $score;
				//echo("<br><br>ADDED NEW SUBGRAPH: "); print_r($new_subgr); echo("<br><br>");
			}
			
			++$counter;
		}
				
		/* print subgraphs with normalized count and score */
		for($i=0; $i < count($subgraphs); ++$i)
		{
			echo("SUBGRAPH WITH COUNT " . $subgraphs_count[$i]/$nr_samples . " AND CONCENTRATION " . $subgraphs_score[$i]/$total_score . ":<br>");
			print_r($subgraphs[$i]);
			echo("<br>Corresponding subgraphs: ");
			print_r($subgraphs_vertices[$i]);
			echo("<br><br>");
		}
	}
	
	/* 
	FUNCTION: returns all permutations - modified from O'Reilly's "PHP Cookbook" for listing all permutations
	*/
	
	function pc_permute($items,&$permutations,$perms = array()) 
	{
		if (empty($items)) 
		{ 
			//print join(' ', $perms) . "<br>";
			$permutations[] = $perms;
		}  
		else 
		{
			for ($i = count($items) - 1; $i >= 0; --$i) 
			{
				 $newitems = $items;
				 $newperms = $perms;
				 list($foo) = array_splice($newitems, $i, 1);
				 array_unshift($newperms, $foo);
				 pc_permute($newitems, $permutations, $newperms);
			}
		}
    }
	
	/* 
	FUNCTION: This function should be called two times, each time with g1 and g2 reversed. If it returns true both times, then g1 and g2 are equal (isomorphic, i.e. the same graphs with different node labels).
	*/
	function onesided_equal_graphs($g1, $g2, $gmap)
	{
		// echo("<br>g1:");
		// print_r($g1);
		// echo("<br>g2:");
		// print_r($g2);
		// echo("<br>gmap:");
		// print_r($gmap);
		
		if(count($g1) != count($g2)) { return 0; }	/* must have the same nr of nodes with links */
		
		foreach($g1 as $fakenode1_g1 => $fakenodes2_g1)
		{
			$node1 = $gmap[$fakenode1_g1];	/* e.g map[5] = 1 OR map[1] = 5 ... you can tweak this in the parameters */
			foreach($fakenodes2_g1 as $fakenode2_g1 => $edges_g1)
			{
				$node2 = $gmap[$fakenode2_g1];
				$edges_g2 = $g2[$node1][$node2];
				if(count($edges_g1) != count($edges_g2)) { return 0; }	/* there must be the same nr of links between the two nodes */
			
				foreach($edges_g1 as $edge_g1)
				{
					if(!in_array($edge_g1,$edges_g2)) {return 0; }
				}
			}
		}
		
		return 1;	/* if all checks are ok, then g1 is <= g2 */
	}


?>