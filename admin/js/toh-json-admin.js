jQuery(document).ready(function () {
	window.UIkit = UIkit;
	var input = jQuery("#TOHfiles");
	input.on("change", function (event) {
		handleFileSelect(event);
	});

	var json;
	jQuery("#genJson").on("click",function(e){
		e.preventDefault();
	const memoryJson = jQuery("#memoryJson");

		console.log("TEST");
		var randstr = Math.random().toString(36).slice(2);
		var prompt = window.prompt("Temporary filename:","toh_temp_json_"+randstr);
		if (prompt){
			var bonusRows = getMemoryTableJSON(prompt);
			if (bonusRows){
				var json = JSON.stringify({bonuses: bonusRows});
				/* jQuery.ajax({
					type: "POST",
					url: window.location+"/save",
					data: {
						"filename": prompt,
						"json": json
					},
					success: function (response) {
						console.log(response);
					   
					},
					error: function (errorThrown) {
						console.warn(errorThrown);
					}
				}); */
				//stream a file download.
				console.log(json);
				memoryJson.val(json);
				download(prompt+".json",json);
			}
		}
	
	});
});

function handleFileSelect(evt) {
	var files = evt.target.files; // FileList object

	//console.table(files);
	if (!memoryTable) return;
	// files is a FileList of File objects. List some properties.
	var output = [];
	for (var i = 0, f; f = files[i]; i++) {
		var filename = files[i].name;
		var ext = filename.split(".").pop();
		//alert(ext); 
		var reader = new FileReader();

		// Closure to capture the file information.
		reader.onload = (function (theFile) {
			return function (e) {
				//try {

					if (ext === "json") {
						importJsonToMemoryTable(e.target.result);
					} else if (ext === "kml") {
						importKMLToMemoryTable(e.target.result);
					} else {
						alert("This extension is not supported at this time.");
					}


				//} catch (ex) {
					//alert('exeception = ' + ex);
				//}
				showSaveMemoryTableButton();
			}
		})(f);
		reader.readAsText(f);
	}
}
function getMemoryTableJSON(filename){
	var memoryTable = jQuery("#memoryTable");

	if (memoryTable){
		var rows = [];
		memoryTable.find("tr").each(function(i){
			var thisRow = jQuery(this);
			//console.log(i+"---");
			var cells = thisRow.find("td");
			if (cells.length <= 0) return;
			//var cells = [];
			row = {};
			thisRow.find("td").each(function(j){
				val = jQuery(this).text().trim();
				key = jQuery(this).attr("data-key");
				if (val && key && val.length > 0 && key.length > 0) {
					row[key] = val;
				}
			});
			rows.push(row);
		});
		//console.table(rows);
		return rows;
	} else {
		alert("No memory table found");
	}

}
function importJsonToMemoryTable(result) {
	json = JSON.parse(result);
	memoryJson.text(result);
	for (j = 0; j <= json.bonuses.length; j++) {
		if (typeof json.bonuses[j] !== "undefined" && json.bonuses[j] != null) {
			var thisRow = "<tr>";
			Object.keys(json.bonuses[j]).forEach(function (e) {
				//console.log(e);
				thisRow += "<td data-key=\"" + e + "\">" + json.bonuses[j][e] + "</td>";
			});
			thisRow += "</tr>";
			memoryTable.append(thisRow);
		}
	}
}

function importKMLToMemoryTable(result) {
	let parser = new DOMParser()
	let xmlDoc = parser.parseFromString(result, "text/xml")
	let rows = [];
	var placemarks = xmlDoc.getElementsByTagName("Placemark");

	for (i = 0; i < placemarks.length; i++) {
		var x = xmlDoc.getElementsByTagName("Placemark")[i];
		xlen = x.childNodes.length;
		y = x.firstChild;
		for (z = 0; z < xlen; z++) {
			data = y.innerHTML;
			if (data !== undefined){
				
				if (z === 1){ //kml sCode info
					if (data.indexOf("–") != -1) scode = data.split("–")[0].replace("<![CDATA[", "").replace("]]>", "").trim();
					if (data.indexOf("–") != -1) city = data.split("–")[1].trim();

					if (data.indexOf("-") != -1) scode = data.split("-")[0].replace("<![CDATA[", "").replace("]]>", "").trim();
					if (data.indexOf("-") != -1) city = data.split("-")[1].trim();
//console.log(rows[i].sCode);
				} else if (z === 3){ //data field
					var str = data.replace("<![CDATA[", "").replace("]]>", "");
					var info = str.split("<br>");
					var state = '';
					var filtered = [];
					for (a = 0; a < info.length; a++){
						if (info[a] != null && info[a] != "" ) filtered.push(info[a]);
					}
					if ( filtered[0].toLowerCase().indexOf("<img") === 0) filtered.shift();
					filtered.reverse();
					name = filtered.pop().trim();
					access = filtered.filter(s => s.toLowerCase().indexOf("access:") === 0);
					gps = filtered.filter( s => s.toLowerCase().indexOf("gps:") === 0);
					//instructions = filtered.filter( s => s.toLowerCase().indexOf("instructions:") === 0);
					madeinamerica = filtered.filter( s => s.toLowerCase().indexOf("made in america:") === 0);
					citystate = filtered.filter( s => s.indexOf(city+",") === 0 );
					for (v = 0; v < citystate.length; v++){
						if (citystate[v].split(",").length == 2){
							loc = citystate[v].split(",");
							state = loc[1].trim();
						}
					}

					//if ( filtered[0].toLowerCase().indexOf("instructions:") > -1 ) filtered.shift();
					if ( filtered[0].toLowerCase().indexOf("made in america:") > -1 ) filtered.shift();
					if ( filtered[0].toLowerCase().indexOf("access:") > -1 ) filtered.shift();
					if ( filtered[0].toLowerCase().indexOf("gps:") > -1 ) filtered.shift();
					var known_data = ["access:","gps:","made in america:"];

					for (a = 0; a < filtered.length; a++){
						known_data.forEach(function(str,index,array){
							if (filtered[a].toLowerCase().indexOf(known_data[index]) > -1){
								filtered.splice(a,1);
							}
						});
					}
					filtered.reverse();
					var MIA = 'Data not available';
					if (madeinamerica[0] !== undefined){
						MIA = madeinamerica[0].replace("Made in America:","");
					}
					var GPS = 'Data not available';
					if (gps[0] !== undefined){
						GPS = gps[0].replace("GPS:","");
					}
					var ACCESS = 'Data not availalbe';
					if (access[0] !== undefined){
						ACCESS = access[0].replace("Access:","");
					}
		
					rows[i] = {
						sCode: scode,
						sName: name,
						sCity: city,
						sState: state,
						sAccess: ACCESS,
						sGPS: GPS,
						MIA: MIA,
						sFlavor: "",
					};
					rows[i].sAddress = filtered[0];
					filtered.shift();

					var c = 0;
					var datapoints = [];

					while (c < filtered.length) {
						if (typeof filtered[c].trim() !== "undefined"){
							datapoints[i] = [];
							//console.log("FIND: "+rows[i].sCity+", "+rows[i].sState);
							if (filtered[c].trim() === rows[i].sCity+", "+rows[i].sState){
								//filtered.splice(c,1);
							} else {
								datapoints[i].push(filtered[c].trim());
							}
						}
						c++;
					}
					//console.table(datapoints[i]);
					datapoints[i].forEach(function(value,index,array){
						rows[i].sFlavor += value+"\n";
					});
					//console.table(rows[i]);
					//console.table(filtered);

				}
			}
			y = y.nextSibling;
		}
	}
	var convertCat = "TOH";
	for (b = 0; b <= rows.length; b++){
		if (typeof rows[b] !== "undefined" && rows[b] != null){
			var thisRow = "<tr>";
		   
			thisRow += "<td data-key=\"bonusCode\">"+rows[b].sCode+"</td>";
			thisRow += "<td data-key=\"category\">"+convertCat+"</td>";
			thisRow += "<td data-key=\"name\">"+rows[b].sName+"</td>";
			thisRow += "<td data-key=\"address\">"+rows[b].sAddress+"</td>";
			thisRow += "<td data-key=\"city\">"+rows[b].sCity+"</td>";
			thisRow += "<td data-key=\"state\">"+abbrState(rows[b].sState, 'abbr')+"</td>";
			thisRow += "<td data-key=\"GPS\">"+rows[b].sGPS+"</td>";
			thisRow += "<td data-key=\"Access\">"+rows[b].sAccess+"</td>";
			thisRow += "<td class=\"uk-width-medium uk-text-truncate\" data-key=\"flavor\">"+rows[b].sFlavor+"</td>";
			thisRow += "<td data-key=\"madeinamerica\">"+rows[b].MIA+"</td>";
			thisRow += "<td data-key=\"imageName\">2019"+rows[b].sCode.trim().toLowerCase()+".jpg</td>";
			thisRow += "<td data-key=\"sTrophyGroup\">"+abbrState(rows[b].sState, 'abbr')+"</td>";

			thisRow += "</tr>";
			jQuery("#memoryTable").append(thisRow); 
		}
	}
	jQuery("#memoryTable").find("td").each(function(){
		var text = jQuery(this).text();
		if ( text == "undefined"){
			jQuery(this).text("Data not found");
		}
	})
}

function reduceKnownDatapoints(array){
	var known = ["instructions:","made in america:","access:","gps:"];
	for (a = 0; a < array.length; a++){
		for (i = 0; i < known.length; i++){
			if ( array[a].toLowerCase().indexOf(known[i]) > -1 ) array.splice(a,1);
		}
	}
	return array;
}


function showSaveMemoryTableButton(){
	jQuery("#genJson").removeClass("uk-hidden");

}

function download(filename, text) {
	var element = document.createElement('a');
	element.setAttribute('href', 'data:json/application;charset=utf-8,' + encodeURIComponent(text));
	element.setAttribute('download', filename);
	element.style.display = 'none';
	document.body.appendChild(element);
	element.click();
	document.body.removeChild(element);
  }

function abbrState(input, to) {
	if (!input) return '';
	var states = [
		['Arizona', 'AZ'],
		['Alabama', 'AL'],
		['Alaska', 'AK'],
		['Arizona', 'AZ'],
		['Arkansas', 'AR'],
		['California', 'CA'],
		['Colorado', 'CO'],
		['Connecticut', 'CT'],
		['Delaware', 'DE'],
		['Florida', 'FL'],
		['Georgia', 'GA'],
		['Hawaii', 'HI'],
		['Idaho', 'ID'],
		['Illinois', 'IL'],
		['Indiana', 'IN'],
		['Iowa', 'IA'],
		['Kansas', 'KS'],
		['Kentucky', 'KY'],
		['Kentucky', 'KY'],
		['Louisiana', 'LA'],
		['Maine', 'ME'],
		['Maryland', 'MD'],
		['Massachusetts', 'MA'],
		['Michigan', 'MI'],
		['Minnesota', 'MN'],
		['Mississippi', 'MS'],
		['Missouri', 'MO'],
		['Montana', 'MT'],
		['Nebraska', 'NE'],
		['Nevada', 'NV'],
		['New Hampshire', 'NH'],
		['New Jersey', 'NJ'],
		['New Mexico', 'NM'],
		['New York', 'NY'],
		['North Carolina', 'NC'],
		['North Dakota', 'ND'],
		['Ohio', 'OH'],
		['Oklahoma', 'OK'],
		['Oregon', 'OR'],
		['Pennsylvania', 'PA'],
		['Rhode Island', 'RI'],
		['South Carolina', 'SC'],
		['South Dakota', 'SD'],
		['Tennessee', 'TN'],
		['Texas', 'TX'],
		['Utah', 'UT'],
		['Vermont', 'VT'],
		['Virginia', 'VA'],
		['Washington', 'WA'],
		['West Virginia', 'WV'],
		['Wisconsin', 'WI'],
		['Wyoming', 'WY'],
	];

	if (to == 'abbr') {
		input = input.replace(/\w\S*/g, function (txt) {
			return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
		});
		for (i = 0; i < states.length; i++) {
			if (states[i][0] == input) {
				return (states[i][1]);
			}
		}
	} else if (to == 'name') {
		input = input.toUpperCase();
		for (i = 0; i < states.length; i++) {
			if (states[i][1] == input) {
				return (states[i][0]);
			}
		}
	}
}
