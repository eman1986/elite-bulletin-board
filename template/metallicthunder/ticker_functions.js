/* Created by: James Crooke :: http://www.cj-design.com*/

var list; // global list variable cache
var tickerObj; // global tickerObj cache
var hex = 255;

function fadeText(divId)
{
	if(tickerObj)
	{
		if(hex>0)
		{
			hex-=5; // increase color darkness
			tickerObj.style.color="rgb("+0+","+0+","+0+")";
			setTimeout("fadeText('" + divId + "')", fadeSpeed); 
		}
		else
			hex=255; //reset hex value
	}
}

function initialiseList(divId)
{
	tickerObj = document.getElementById(divId);
	if(!tickerObj)
		reportError("Could not find a div element with id \"" + divId + "\"");
	
	list = tickerObj.childNodes;
	if(list.length <= 0)
		reportError("The div element \"" + divId + "\" does not have any children");
		
	for (var i=0; i<list.length; i++)
	{
		var node = list[i];
		if (node.nodeType == 3 && !/\S/.test(node.nodeValue)) 
	        		tickerObj.removeChild(node);

	}
	run(divId, 0);
}
function run(divId, count)
{
	fadeText(divId);
	list[count].style.display = "block";
	if(count > 0)
		list[count-1].style.display = "none";
	else
		list[list.length-1].style.display = "none";
	
	count++;
	if(count == list.length)
		count = 0;
		
	window.setTimeout("run('" + divId + "', " + count+ ")", interval*1000);
}
function reportError(error)
{
	alert("The script could not run because you have errors:\n\n" + error);
	return false;
}