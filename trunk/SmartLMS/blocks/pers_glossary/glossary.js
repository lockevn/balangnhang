		var seldiv = document.getElementById('termSelector');
		var statdiv = document.getElementById('status');
		var letters = new Object();
		var letter_arr = new Array();		
		var lastselwhich = 1;		
		var dh = ROOT_URL + "/blocks/pers_glossary/data.php";
		
		

    	function renderStatus(o)
			{
				if (o.responseText !== undefined)
				{
					var ret = from_json(o.responseText);
					if (ret.ok)
					{
						statdiv.innerHTML = "Success";
						statdiv.style.backgroundColor = '#33ff33';
						populateSelector();
					}
					else
					{
						statdiv.innerHTML = "Failed";
						statdiv.style.backgroundColor = '#ff3333';
					}
				}
			}

    
   
	    	function renderSelector(o) {
	    	
				if (o.responseText !== undefined)
				{
					var ldhtml = '<div id="letterdiv" style="border-bottom: 1px solid #000"></div>';
					varlisthtml = '<div id="termlist" style="height:200px;overflow:auto"></div>';
					seldiv.innerHTML = ''; //o.responseText;
					seldiv.innerHTML += ldhtml;
					seldiv.innerHTML += varlisthtml;
					var letterdiv = document.getElementById('letterdiv');
					letters = new Object();
					letter_arr = new Array();
			
					var ret = from_json(o.responseText);
					if (ret.ok == 0)
						return;
					terms = ret.terms;
					for (id in terms)
					{
						var t = terms[id];
						var startc = t.sl_value.charAt(0).toUpperCase();
						if (!letters[startc])
						{
							//console.log("hum..");
							letters[startc] = new Array();
							letter_arr.push(startc);
						}
						letters[startc].push(t);
						//console.log(id, startc, t);
						//console.log(letters);
					}
			
					letter_arr = letter_arr.sort();
					for (l in letter_arr)
					{
						letter = letter_arr[l];
						letterdiv.innerHTML += '<a href="javascript:void(0)" onclick="showletter(\''+letter+'\')">'+letter+'</a> ';
					}
					letterdiv.innerHTML += '<a href="javascript:void(0)" onclick="showAllLetter(letter_arr)">All</a> ';
					// show the first letter stuff
					//showletter(letter_arr[0]);
					showAllLetter(letter_arr);
				}
			}
    

    	function showAllLetter(letter_arr)
		{
			var tmpStr = '<table width="100%">';
			var tl = document.getElementById('termlist');
			var startc;			
			for(var j = 0; j < letter_arr.length; j++)
			{
				startc = letter_arr[j];
				for (var i = 0; i < letters[startc].length; i++)
				{
					var term = letters[startc][i];
					tmpStr += '<tr>';
					tmpStr += '<td width="80%"><b>'+term.sl_value+'</b>:' + term.tl_value + '</td>';
					tmpStr += '<td width="10%"><a href="javascript:void(0)" onclick="deleteterm(\''+ startc +'\','+i+')"><img alt="Delete" src="' + PIXPATH + '/t/delete.gif"/></a></td>';
					tmpStr += '<td width="10%"><a href="javascript:void(0)" onclick="editterm(\''+ startc +'\','+i+')"><img alt="Edit" src="' + PIXPATH + '/t/edit.gif"/></a></td>';
					tmpStr += '</tr>';									
				}
			}
			tmpStr += '</table>';
			tl.innerHTML = tmpStr;
		}
 
    
 
    	function showletter(startc)
		{
			var tl = document.getElementById('termlist');
			var tmpStr = '<table width="100%"><tbody>';
			for (var i = 0; i < letters[startc].length; i++)
			{
				var term = letters[startc][i];
				tmpStr += '<tr>';
					tmpStr += '<td width="80%"><b>'+term.sl_value+'</b>:' + term.tl_value + '</td>';
					tmpStr += '<td width="10%"><a href="javascript:void(0)" onclick="deleteterm(\''+ startc +'\','+i+')"><img alt="Delete" src="' + PIXPATH + '/t/delete.gif"/></a></td>';
					tmpStr += '<td width="10%"><a href="javascript:void(0)" onclick="editterm(\''+ startc +'\','+i+')"><img alt="Edit" src="' + PIXPATH + '/t/edit.gif"/></a></td>';
					tmpStr += '</tr>';					
			}
			tmpStr += '</tbody></table>';
			tl.innerHTML = tmpStr;
		}

    

    	function populateSelector()
		{
			var qs;
			var ws;		
		
			qs = '?data='+encodeURIComponent(to_json({act: 'get'}));			
			var callback = {
				success: renderSelector,
				failure: handleFailure,
			};
			var request = YAHOO.util.Connect.asyncRequest('GET', dh+qs, callback); 
		}
    
    
    
    	function saveForm()
		{
			// get the form
			var f = document.getElementById('termform');
			var act = 'insert';
			if (f.id.value != -1)
				act = 'edit';
		
			var qs = '?data='+encodeURIComponent(to_json({	act: act,							
									sl_value: f.sl_value.value,
									tl_value: f.tl_value.value,							
									id: f.id.value}));
			var callback = {
				success: renderStatus,
				failure: handleFailure,
			};
			var request = YAHOO.util.Connect.asyncRequest('GET', dh+qs, callback); 
		
    	}
    

		function editterm(startc,i)
		{
			var term = letters[startc][i];
			var f = document.getElementById('termform');
			// add hidden id field for editing
			f.id.value = term.id;
			// populate form
			f.sl_value.value = term.sl_value;
			f.tl_value.value = term.tl_value;

		}
    

		function deleteterm(startc,i)
		{
			var term = letters[startc][i];
			// get the form
			var f = document.getElementById('termform');
			//f.id.value = term.id;
			var act = 'delete';
			var qs = '?data='+encodeURIComponent(to_json({	act: act,																								
									id: term.id}));
			var callback = {
				success: renderStatus,
				failure: handleFailure,
			};
			var request = YAHOO.util.Connect.asyncRequest('GET', dh+qs, callback);
			
		}
    
 	
    	function handleFailure(o)
		{
			/*alert("xmlhttprequest failed!\nTransaction id: "+o.tId+"\nHTTP status: "+o.status+"\nStatus text: "+o.statusText);*/
			 alert("xmlhttprequest failed!");
		}
		
		function newEntry()
		{
			var f = document.getElementById('termform');			
			f.id.value = -1;
			f.sl_value.value = '';
			f.tl_value.value = '';
			
		}	
		
		populateSelector();
		
		
		
