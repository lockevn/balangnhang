function from_json (str) { 
  var x; 
  if (!str)
    str="{error:'missing response'}";
  eval('x = (' + str + ");");
  return x;
}

function json_url (method,args,noescape) {
  var url = "http://localhost:8001/" + method;
  var rnd = Math.random(); // prevent client-side caching
  var json = noescape ? args : escape(to_json(args));
  if (! noescape) json = json.replace(/\+/g, "%2b");
  return url + '?json=' + json + ';rnd=' + rnd;
}

// Source for to_json(): http://www.crockford.com/JSON/js.html - (c) json.org.
// License: BSD-style (with addendum: "The Software shall be used for Good, not Evil")
function to_json(arg) {
  var c, i, l, o, u, v;
  switch (typeof arg) {
  case 'object':
	if (arg) {
	  if (arg.constructor == Array) {
		o = '';
		for (i = 0; i < arg.length; ++i) {
		  v = to_json(arg[i]);
		  if (o) {
			o += ',';
		  }
		  if (v !== u) {
			o += v;
		  } else {
			o += 'null,';
		  }
		}
		return '[' + o + ']';
	  } else if (typeof arg.toString != 'undefined') {
		o = '';
		for (i in arg) {
		  v = to_json(arg[i]);
		  if (v !== u) {
			if (o) {
			  o += ',';
			}
			o += to_json(i) + ':' + v;
		  }
		}
		return '{' + o + '}';
	  } else {
		return;
	  }
	}
	return 'null';
  case 'unknown':
  case 'undefined':
  case 'function':
	return u;
  case 'string':
	l = arg.length;
	o = '"';
	for (i = 0; i < l; i += 1) {
	  c = arg.charAt(i);
	  if (c >= ' ') {
		if (c == '\\' || c == '"') {
		  o += '\\';
		}
		o += c;
	  } 
      else {
		switch (c) {
		case '\b':
		  o += '\\b';
		  break;
		case '\f':
		  o += '\\f';
		  break;
		case '\n':
		  o += '\\n';
		  break;
		case '\r':
		  o += '\\r';
		  break;
		case '\t':
		  o += '\\t';
		  break;
		default:
		  c = c.charCodeAt();
		  o += '\\u00' + Math.floor(c / 16).toString(16) + (c % 16).toString(16); // /;
		}
	  }
	}
	return o + '"';
  default:
	return String(arg);
  }
}
