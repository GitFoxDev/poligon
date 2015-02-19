function getXMLDocument(url)
{
	var xml;
	if(window.XMLHttpRequest)
	{
		xml=new window.XMLHttpRequest();
		xml.open("GET", url, false);
		xml.send("");
		return xml.responseXML;
	}
	else
		if(window.ActiveXObject)
		{
			xml=new ActiveXObject("Microsoft.XMLDOM");
			xml.async=false;
			xml.load(url);
			return xml;
		}
		else
		{
			return null;
		}
}

function getAttributes(node)
{
    var ret = new Object();
    if(node.attributes)
      for(var i=0; i<node.attributes.length; i++)
      {
          var attr = node.attributes[i];
          ret[attr.name] = attr.value;
      }
    return ret;
}

function drawMenu(url, div)
{
	var div = document.getElementById(div);
	if (!div) return;
	var output = parseXMLMenu(url);
	div.innerHTML = output;
}

function parseXMLMenu(url)
{
	var output = "";
	var xml = null;
	try
	{
		xml = getXMLDocument(url);
		if(!xml) return "<font color='red'>Нет данных</font>";
	}
	catch(e)
	{
		return "<font color='red'>"+e.message+"</font>";
	}
	var items = xml.getElementsByTagName("item");
	var item = null;
	if (items)
		for(var i1=0; i1<items.length; i1++)
		{
			item = items[i1];
			var tw_attr = getAttributes(item);
			var t_town = "<li><a href='" + tw_attr['link'] + "' id='" + tw_attr['id'] + "'>" + tw_attr['name'] + "</a></li>";
			output += t_town;
		}
	return "<ul>" + output + "</ul>";
}