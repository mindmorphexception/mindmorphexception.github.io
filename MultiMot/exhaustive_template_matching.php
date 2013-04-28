<?
	/* 
	Author: Iulia Maria Comsa
	Email:  iulia.comsa1@gmail.com
	*/

/* ================================================================= */
/* =========== EDGE MATCHING ALGORITHM FUNCTIONS BEGIN ============= */
/* ================================================================= */

$template_matched = array();

/* FUNCTION: find all motifs in the graph by edge matching */
function edge_match()
{
	global $motifs;
	global $nr_nodes;
	global $node_list;
	global $template_matched;
	$template_matched = array();
	for($i = 0; $i < count($motifs); ++$i)	/* for each motif */
	{
		echo("<br/><br/>Matching motif #" . ($i+1) . "...<br/><br/>");
		for($node = 0; $node < $nr_nodes; ++$node)		/* for each possible start node */
		{
			backtrack($motifs[$i], array($motifs[$i][0]["node1"] => $node_list[$node]), array());
		}
	}
}

/* FUNCTION: main backtracking function for the motif finder exact algorithm (by edge matching) */
function backtrack($motif, $map, $used)
{
	global $graph;
	global $template_matched;
	if(count($motif) == 0)	/* if current motif is empty, we have matched it completely */
	{
		if(!in_array($map,$template_matched))	/* also make sure we haven't displayed this mapping multiple times */
		{
			$template_matched[] = $map;
			print_ok("Found a match:<br/>");
			print_r($map);	/* print mapping */			
		}
		return;		/* quit the function */
	}
	
	/* otherwise, we still have to match some parts of the motif */
	$edge = array_shift($motif);	/* remove the first edge from array */
	$next = match_possible($edge, $map, $used);	/* find possible matches for the edge */
	//echo("<br><br>"); print_r($next);echo("<br><br>");
	
	for($i = 0; $i < count($next); ++$i)	/* for each possible edge */
	{
		$node1 = $next[$i]["node1"];	/* source node */
		$node2 = $next[$i]["node2"];	/* destination node */
		$edgeval = $next[$i]["edge"];	/* edge value */
		
		$newmap = $map;
		$newmap[$edge["node1"]] = $node1;	/* assign in the map just in case they're not already there - */
		$newmap[$edge["node2"]] = $node2;	/* - the match_possible function above checks that the mapping is okay */
		
		$newused = $used;
		$newused[] = array($node1,$node2,$edgeval);	/* mark the edge in the graph as used */
		
		backtrack($motif, $newmap, $newused);
	}
		
}


/* FUNCTION: return all edges from the graph that can be mapped to the given edge */
/* returns an array where each element has an "index" (the edge nr in the graph), "node1", "node2" and "edge" */
function match_possible($edge, $map, $used)
{
	global $graph;
	global $graph_invers;
	$result = array();
	if(isset($map[$edge["node1"]]) && isset($map[$edge["node2"]]))	/* if both nodes of the motif are already mapped */
	{
		$node1_graph = $map[$edge["node1"]];
		$node2_graph = $map[$edge["node2"]];
		if(isset($graph[$node1_graph][$node2_graph]))	/* if there exists an edge in the graph... */
		{
			$possible_edges = $graph[$node1_graph][$node2_graph]; 	/* there may be multiple edges between the nodes, as this is a multirelational graph */
			for($i=0; $i<count($possible_edges); ++$i)
			{
				if(!in_array(array($node1_graph,$node2_graph,$possible_edges[$i]),$used))	/* if we haven't already matched this edge */
				{
					if($_POST["exact_edge"])	/* if edge value must be identical */
					{
						if(strcasecmp($possible_edges[$i],$edge["edge"]) == 0)	/* and if it is identical */
						{
							$res["node1"] = $node1_graph;		/* then this is the only match and return it */
							$res["node2"] = $node2_graph;
							$res["edge"] = $possible_edges[$i];
							$result[0] = $res;
							return $result;
						}						
					}
					else	/* if edge value must not be identical */
					{
						$res = array();
						$res["node1"] = $node1_graph;		/* then this a possible match and return it - */
						$res["node2"] = $node2_graph;		/* - there may be other possibilities but it makes no sense returning them */
						$res["edge"] = $possible_edges[$i];
						$result[] = $res;
						return $result;
					}
				}
			}
		}
		return $result;	/* if we are here, no matching edge was found */
	}
	
	if(isset($map[$edge["node1"]]))	/* if only node1 is mapped */
	{
		$node1_graph = $map[$edge["node1"]];
		$possible_edges = $graph[$node1_graph];
		if(is_array($possible_edges)) foreach ($possible_edges as $node2_graph => $edges_graph) /* for each of the nodes2 linked to node1 in the graph */
		{
			if(!in_array($node2_graph, $map))	/* if node2 has not already been mapped */
			{
				for($j=0; $j < count($edges_graph); ++$j)	/* for each edge between node1 and node2 */
				{	
					$edge_graph = $edges_graph[$j];
					if(!isset($_POST["exact_edge"]))	/* if the edge must not be identical */
					{
						$res = array();		/* add this possibility */
						$res["node1"] = $node1_graph;
						$res["node2"] = $node2_graph;
						$res["edge"] = $edge_graph;
						$result[] = $res;
					}
					else if(strcasecmp($edge_graph,$edge["edge"]) == 0)	/* if edges must be identical and are identical  */
					{
						$res = array();		/* add this possibility */
						$res["node1"] = $node1_graph;
						$res["node2"] = $node2_graph;
						$res["edge"] = $edge_graph;
						$result[] = $res;
					}
				}
			}
		}
		return $result;
	}
	
	if(isset($map[$edge["node2"]]))	/* if only node2 is mapped */
	{
		$node2_graph = $map[$edge["node2"]];
		$possible_edges = $graph_invers[$node2_graph];
		foreach ($possible_edges as $node1_graph => $edges_graph) /* for each of the nodes1 linked to node2 in the graph */
		{
			if(!in_array($node1_graph, $map))	/* if node1 has not already been mapped */
			{
				for($j=0; $j < count($edges_graph); ++$j)	/* for each edge between node1 and node2 */
				{	
					$edge_graph = $edges_graph[$j];
					if(!isset($_POST["exact_edge"]))	/* if the edge must not be identical */
					{
						$res = array();		/* add this possibility */
						$res["node1"] = $node1_graph;
						$res["node2"] = $node2_graph;
						$res["edge"] = $edge_graph;
						$result[] = $res;
					}
					else if(strcasecmp($edge_graph,$edge["edge"]) == 0)	/* if edges must be identical and are identical */
					{
						$res = array();		/* add this possibility */
						$res["node1"] = $node1_graph;
						$res["node2"] = $node2_graph;
						$res["edge"] = $edge_graph;
						$result[] = $res;
					}
				}
			}
		}
		return $result;
	}
	print_error("Check whether all motif graphs are connected.");
	return $result; /*if we are here, node1 and node2 are both not mapped - but this can't be right if the motif is connected and its edges are sorted */
	
}

/* ================================================================= */
/* =========== EDGE MATCHING ALGORITHM FUNCTIONS END =============== */
/* ================================================================= */
?>