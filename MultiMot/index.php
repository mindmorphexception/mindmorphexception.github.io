<!DOCTYPE html>

<html>

<head>
	<title> MultiMot: Motif finder in multirelational graphs </title>
	<meta name="description" content="Motif finder in multirelational graphs">
	<meta name="keywords" content="multirelational graphs,subgraphs,motif finder">
	<meta name="author" content="Iulia Maria Comsa">
	<link rel="stylesheet" type="text/css" href="style.css">
	<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-2">
	<link href='http://fonts.googleapis.com/css?family=Caudex:400italic' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Cardo:400,700' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Noto+Serif:700' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Inconsolata' rel='stylesheet' type='text/css'>
	
	<?php
	require "exhaustive_template_matching.php"; 
	require "subsample.php"; 
	$execution_time = 120;
	?>
	
	<script type="text/javascript">
		/* copy the output text to clipboard */
		function download_results()
		{
			var content =  this.document.getElementById("output_div").innerText
			if(content == null) content = this.document.getElementById("output_div").textContent; 
			var form = document.createElement('form');
			form.setAttribute('method', 'POST');
			form.setAttribute('action', 'download_results.php');
			var myvar = document.createElement('input');
			myvar.setAttribute('name', 'data');
			myvar.setAttribute('type', 'hidden');
			myvar.setAttribute('value', content);
			form.appendChild(myvar);
			document.body.appendChild(form);
			form.submit();
		}
		
		/* disable checkboxes when changing motif finding mode + hide motifs textarea*/
		function disable_options()
		{
			document.getElementById('motifs_textarea').style.display='none';
			document.getElementById('edge_value_checkbox').disabled = true;
		}
		
		/* enable checkboxes when changing motif finding mode + show motifs textarea */
		function enable_options()
		{
			document.getElementById('motifs_textarea').style.display='block';
			document.getElementById('edge_value_checkbox').disabled = false;
		}
		
		
		function body_loaded()
		{
			/* enable/disable checkboxes depending on the selected motif finding mode */
			if(document.getElementById("subsample_radiobutton").checked)
			{
				document.getElementById('edge_value_checkbox').disabled = true;
			}
			else
			{
				document.getElementById('edge_value_checkbox').disabled = false;
			}
			
			/* fill graph textarea */
			//document.getElementById("graph_textarea").value = "a\r\nbbbbb";
		}
	</script>
</head>

<body onload="body_loaded()">
<div id="title_div"> <strong> MultiMot </strong> </div>
<div id="subtitle_div">  <i> Motif finding in multirelational graphs </i> </div>
<div id="wrap">		
	<div id="graph_panel"> <!-- GRAPH COLUMN -->
		<div class="col_headline"> Graph </div>
		
		<!-- file upload form -->
		<form method="post"
			enctype="multipart/form-data">
			<label for="file">You can paste the graph data below or upload it from a file:</label>
			<input type="file" name="file" id="file"><br>
			<input type="submit" name="submit" value="Upload">
		</form>
		
		<!-- graph form -->
		<form method="post" > 
		<textarea id="graph_textarea" title="Format: node1 node2 edge" name="graph" cols=30 rows=15><?php 
		/* fill textarea */
		if(isset($_POST["graph"])) echo(trim($_POST["graph"])); 
		else if(isset($_FILES["file"]))
		{
			if ($_FILES["file"]["error"] > 0)
				echo "Error: " . $_FILES["file"]["error"];
				
			else if ($_FILES["file"]["size"] > 10000000) 
				echo "Error: Your file is too large.";
				
			else if($_FILES["file"]["type"] != "text/plain")
				echo "Error: Your file is not a text/plain file.";
				

			else
			{
				echo file_get_contents($_FILES['file']['tmp_name']);
			}
		} 
		else echo("node_a node_b edge1
node_a node_d edge1
node_e node_a edge1
node_b node_e edge1
node_d node_a edge1
node_e node_f edge1"); 
		?></textarea> 
		
		<!--button type="button" class="load_file_div" onclick="loadfile()"> Load from file </button-->
		
		<br/><br/>
		
		<input type="checkbox" name="is_directed" id="is_directed_checkbox" value=1 <?php if(isset($_POST["is_directed"])) echo('checked="checked"'); ?>> The graph is directed <br/>
		<input type="checkbox" name="exact_edge" id="edge_value_checkbox" value=1 disabled="true" <?php if(isset($_POST["exact_edge"])) echo('checked="checked"'); ?>> Edges in the motif must have the same value as in the graph in order to match (<i>only in template matching mode</i>) <br/>
		<!--input type="checkbox" name="allow_multiple_links" value=1 <?php if(isset($_POST["allow_multiple_links"])) echo('checked="checked"'); ?>> Allow more links between matched nodes in the graph than specified in the motif <br/-->
		<input type="submit" value="Compute" name="submit_form" class="button" />
	</div>
	<!-- GRAPH COLUMN ENDS HERE -->
	
	<!-- MOTIF COLUMN -->
	<div id="motif_panel">
		<div class="col_headline"> Motifs </div>
		<input type="radio" name="algorithm" value="subsample" id="subsample_radiobutton"
			<?php 
			if(!isset($_POST["algorithm"]) || (isset($_POST["algorithm"]) && !strcmp($_POST["algorithm"],"subsample"))) 
				echo('checked="checked"'); 
			?> 
			onchange="disable_options()">
			Subsample subgraphs of size
			<select name="subgraph_size">
				<option value="3">3</option>
				<option value="4">4</option>
			</select> nodes
		<br />
		Number of subgraphs to sample:
		<input type="text" name="nr_samples" size=3 value="100">
		<br /><br />
		<input type="radio" name="algorithm" value="match_templates" id="match_templates_radiobutton"
			<?php 
			if(isset($_POST["algorithm"]) && !strcmp($_POST["algorithm"],"match_templates")) 
				echo('checked="checked"'); 
			?> 
			onchange="enable_options()">
			Find all templates<br />
		<textarea id="motifs_textarea" name="motifs" title="Format: number of edges, followed by lines of the form: node1 node2 edge" rows=15 cols=20 
		<?php if(isset($_POST["algorithm"]) && !strcmp($_POST["algorithm"],"match_templates")) echo('style="display:block"'); 
		else echo('style="display:none"');
		?>
		><?php
		if(isset($_POST["motifs"])) echo(trim($_POST["motifs"])); /* fill motif textarea */
		else echo("3
a b edge1
b c edge1
c a edge1
2
a b edge1
b a edge1
4
a b edge1
b c edge1
c a edge1
a d edge1");
		?></textarea>		
	</div>
	<!-- MOTIF COLUMN ENDS HERE -->
	</form>
</div>	

<hr>


<button type="button" class="load_file_div" onclick="download_results()"
<?php
if(!isset($_POST["graph"])) echo ' style="display:none"';
?>
> Download results </button>


<div id="output_div">



<?php 

/* MAIN COMPUTATION BEGINS HERE */

if(isset($_POST["graph"]))
{
	$s = mb_strtoupper(trim($_POST["graph"]));
	
	/* make sure there are values at all for the graph */
	if (strlen($s) == 0)
	{
		print_error("Please enter a graph.");
		return;
	}
	
	/* make an array of values for the graph */
    $vals = preg_split("/[\n \r \r\n ' ' \t]/", $s,-1,PREG_SPLIT_NO_EMPTY);
	
	/* check if there are 3x values in the graph */
	if(count($vals)%3 != 0)
	{
		print_error("The graph must be formatted as: node1 node2 edge");
		return;
	}
	
	/* construct the graph as a list of neighburs for each vertex */
	$graph = array();	/* this keeps the normal edges */
	$graph_invers = array();	/* this keeps the inverse edges (so that we can access easily the "parents" of a node; this only makes sense for a directed graph */
	$graph_edgelist = array();	/* this keeps a list of edges */
	$nr_nodes = 0;
	$nr_edges = 0;
	$node_list = array();
	
	for($i = 0; $i < count($vals); $i = $i+3)
	{
		//$node1 = intval($vals[$i]); if($node1 > $nr_nodes) $nr_nodes = $node1;	/* read source node and update the no of nodes */
		//$node2 = intval($vals[$i+1]);  if($node2 > $nr_nodes) $nr_nodes = $node2; /* read destination node and update the no of nodes */
		//if($node1 < 1 || $node2 < 1) { print_error("One of the values is not correct. Values must be positive integers."); return; } /* check the values */
		$node1 = $vals[$i];
		$node2 = $vals[$i+1];
		if(!in_array($node1,$node_list)) $node_list[] = $node1;
		if(!in_array($node2,$node_list)) $node_list[] = $node2;
		
		if(!isset($graph[$node1][$node2])) $graph[$node1][$node2] = array();	/* create array if it does not exist already */
		
		$edge = trim($vals[$i+2]);

		if(in_array($edge,$graph[$node1][$node2]))
		{
			print_warning("Multiple identical edges found between nodes " . $node1 . " and " . $node2 . " with value " . $edge . ".");
		}
		else
		{
			$graph[$node1][$node2][] = $edge;	/* add edge */
			if(!isset($_POST["is_directed"])) { $graph[$node2][$node1][] = $edge; $graph_invers[$node1][$node2][] = $edge; }		/* if the graph is not directed, add 2 edges */
			else $graph_invers[$node2][$node1][] = $edge;		/* else, keep the inverse edge in the inverse graph */
			$graph_edgelist[] = array($node1,$node2,$edge);
			$nr_edges++;
		}
	}
	$nr_nodes = count($node_list);
	
	print_ok("Graph parsing successful.");
	echo("The number of nodes is " . $nr_nodes . ".<br/>");
	echo("Found " . $nr_edges . " edges in the graph.<br/>");
	if($_POST["is_directed"]) echo("The graph is directed.<br/>");
	else echo("The graph is undirected.<br/>");
	if($_POST["exact_edge"]) echo("Edge value must be the same in the graph and in the matched motif.<br/>");
	else echo("Edge value will be ignored.<br/>");
	/*if($_POST["allow_multiple_links"]) echo("Additional links allowed between nodes for matching.<br/>");
	else echo("Additional links not allowed between nodes for matching.<br/>");*/
	
	/* pick algorithm */
	if(!strcmp($_POST["algorithm"],"match_templates"))	/* exhaustive template matching */
	{	
		$m = mb_strtoupper(trim($_POST["motifs"]));
		
		/* make sure there are values at all for the motifs */
		if (strlen($m) == 0)
		{
			print_error("Please enter at least a motif.");
			return;
		}
		
		$motifs = load_motifs_textarea($m);
		print_ok("Motif parsing successful.");
		echo("Found " . count($motifs) . " motifs.<br/>");
		
		print_ok("Starting edge matching algorithm...");
		edge_match();
	}
	
	elseif(!strcmp($_POST["algorithm"],"subsample"))	/* subsampling with probabilities */
	{	
		print_ok("Starting subsampling algorithm...");
		subsample($_POST["subgraph_size"],$_POST["nr_samples"]);
	}
	
	print_ok("Algorithm finished.");
	
	echo "<hr>";
}
?>

<?php

/* FUNCTION: print an error (red) */
function print_error($message)
{
	echo('<div class="error">Error: ' . $message . '</div>');
}

/* FUNCTION: print a happy message (green) */
function print_ok($message)
{
	echo('<div class="ok">' . $message . '</div>');
}

/* FUNCTION: print a warning (orange) */
function print_warning($message)
{
	echo('<div class="warning">Warning: ' . $message . '</div>');
}

/* FUNCTION: Load motifs from string */
function load_motifs_textarea($s)
{
	$motifs = array();
	$nr_motifs = 0;
	
	/* make an array of values */
    $vals = preg_split("/[\n \r \r\n ' ' \t]/", $s,-1,PREG_SPLIT_NO_EMPTY);
	
	$i = 0;
	while($i < count($vals))
	{
		$nr_edges = intval($vals[$i]); 
		if($nr_edges < 1) { print_error("The number of edges of every motif must be an integer"); return; }
		$current_nodes = array();	/* keeps the nodes that were mentioned so far in the motif */
		for($j = 0; $j < $nr_edges; ++$j)
		{
			/*$node1 = intval($vals[$i+$j*3+1]);
			if($node1 < 1) { print_error("The motif nodes must be positive integers."); return; }
			$node2 = intval($vals[$i+$j*3+2]);
			if($node2 < 1) { print_error("The motif nodes must be positive integers."); return; }
			if($j == 0 && $node1 != 1 && $node2 != 1) { print_error("The first edge in every motif must contain node 1."); return; }*/
			$node1 = $vals[$i+$j*3+1];
			$node2 = $vals[$i+$j*3+2];
			if($j > 0 && !in_array($node1,$current_nodes) && !in_array($node2,$current_nodes)) { print_error("Every edge of the motif except the edge on the first line must contain a node that has been mentioned in a previous edge of that motif."); return; }
			$current_nodes[] = $node1;
			$current_nodes[] = $node2;
			$edge = trim($vals[$i+$j*3+3]);
			$motifs[$nr_motifs][] = array("node1" => $node1, "node2" => $node2, "edge" => $edge);		/* add current edge to list */
		}
		$i = $i + $nr_edges*3 + 1;
		++$nr_motifs;
	}
	return $motifs;
}

/* FUNCTION: Load motifs from a s, as a list of neighbours
The file is structured as:
nr_lines (the nr of lines describing the current motif)
node1 node2 edge (lines describing the motif)
node1 node2 edge
..(nr_lines times)
nr_lines
node1 node2 edge
...
*/
function load_motifs_as_neighbours($filename)
{
	$motifs = array();	/* this array keeps the motifs */
	$f = fopen($filename,"r");	/* open the motifs file */
	$nr_motifs = 0;
	while($nr_edges = fscanf($f,"%d")) /* read number of edges in current motif */
	{
		$nr_edges = $nr_edges[0];	/* for some reason, $nr_edges is read as an array of size 1 above... */
		$edges = array();	/* we store the current motif here */
		for($i = 0; $i < $nr_edges; ++$i)	/* read edges */
		{
			fscanf($f,"%d %d %s",$node1,$node2,$edge);
			$edge = trim($edge);
			$edges[$node1][$node2] = $edge;		/* the graph is kept as a list of neighbours for each vertex */
		}
		$nr_motifs++;	
		$motifs[$nr_motifs] = $edges;	/* save motif to graph */		
	}
	fclose($f);	
	return $motifs;
}

/* FUNCTION: Load motifs from a file, as a list of (node1 node2 node3)
See above for the file description.
*/
function load_motifs_as_edgelist($filename)
{
	$motifs = array();	/* this array keeps the motifs */
	$f = fopen($filename,"r");	/* open the motifs file */
	$nr_motifs = 0;
	while($nr_edges = fscanf($f,"%d")) /* read number of edges in current motif */
	{
		$nr_edges = $nr_edges[0];	/* for some reason, $nr_edges is read as an array of size 1 above... */
		$edges = array();	/* we store the current motif here */
		for($i = 0; $i < $nr_edges; ++$i)	/* read edges */
		{
			fscanf($f,"%d %d %s",$node1,$node2,$edge);
			$edge = trim($edge);
			$motifs[$nr_motifs][] = array("node1" => $node1, "node2" => $node2, "edge" => $edge);		/* add current edge to list */
		}
		$nr_motifs++;	
	}
	fclose($f);	
	return $motifs;
}

/* FUNCTION: Sort a list of edges (node1 node2 edge) of a motif so that at least one of the nodes has appeared before in the list, except the first element */
/* Assumption: the motif is a connected graph */
// function sort_edges(&$edges)		THIS FUNCTION IS NOT YET...FUNCTIONAL
// {
	// if($edges[0]["node1"] != 1 && $edges[0]["node2"] != 1)	/* the first edge must contain node 1 */
	// {
		// for($i = 1; $i < count($edges); ++$i)
		// {
			// if($edges[$i]["node1"] == 1 || $edges[$i]["node2"] == 1)	/* swap first edge */
			// {
				// $aux = $edges[0];
				// $edges[0] = $edges[$i];
				// $edges[$i] = $aux;
				// break;
			// }
		// }
	// }
// }


 
?>

</div>


<div id="infodiv"> 
Download MultiMot's User Guide <a href="MultiMot-UserGuide.txt">here</a>. <br> 
Developed by Iulia Comsa, with the supervision of Dr Crina Grosan. <br> 
The Chrome browser (version 26 as of now) is recommended for running this software. <br>
</div>

</body>
</html>