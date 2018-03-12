
function hide_buttons(hideclass,tg){
    //    alert(hideclass);
    var id= document.getElementsByClassName(hideclass);
    for (var i=0;i<id.length;i++){    
        if(id[i].style.display!='none')
            id[i].style.display='none';
        else
            id[i].style.display='block';
    }
    
    var t=document.getElementById(tg);
    if(t.value.search("hide")==-1)
        t.value=t.value.replace("show","hide");
    else
        t.value=t.value.replace("hide","show");
}

function PrintDiv(el) {//prints the div with the element el - mostly used to print individual or whole graphs
    var elparent=el.parentNode;//gets parent of element of el
    el.style.display='none';
    var id= document.getElementsByClassName("printbuttons");//removes any buttons that maybe in picture
    var display_values=[];//creates an empty vector
    for (var i=0;i<id.length;i++){
        display_values.push(id[i].style.display);
        id[i].style.display='none';
    }

    var id= document.getElementsByClassName("removebuttons");//removes any buttons that maybe in picture
    var display_values=[];//creates an empty vector
    for (var i=0;i<id.length;i++){
        display_values.push(id[i].style.display);
        id[i].style.display='none';
    }
    
    var printContents=elparent.innerHTML
    el.style.display='block';
    for (var i=0;i<id.length;i++){
        id[i].style.display=display_values.pop();
    }
    var originalContents=document.body.innerHTML;

    if(navigator.userAgent.toLowerCase().indexOf('chrom')>-1 || navigator.userAgent.toLowerCase().indexOf('safari')>-1){
        w=window.open('','Print','height=400,width=600');
        w.document.write('<!DOCTYPE html><body><div>');
        w.document.write(printContents);
        w.document.write('</div></body></html>');
        w.document.close();
        w.focus()
        w.onload=function(){w.print();
                          w.close();}
    }else{
        document.body.innerHTML=printContents;
        window.print();
        document.body.innerHTML=originalContents;
    }
}

function update_options() {//function that hides elements based upon which typeselect is chosen
    var e=document.getElementById("typeselect");
    var val=e.options[e.selectedIndex].value;//gets the type select

    document.getElementById("huginform").submit();//submits the form but may not actually have an effect
    switch(val){
    case "heatmap":
        document.getElementById("fire").style.display="none";
        document.getElementById("tad").style.display="none";
        document.getElementById("SE").style.display="none";
        document.getElementById("ctcf").style.display="none";
        document.getElementById("chipseq").style.display="none";
        document.getElementById("toggle").style.display="block";
        document.getElementById("pvaluey").style.display="none";
        break;
    case "browser":
        document.getElementById("fire").style.display="";
        document.getElementById("tad").style.display="";
        document.getElementById("SE").style.display="";
        document.getElementById("ctcf").style.display="";
        document.getElementById("chipseq").style.display="";
        document.getElementById("toggle").style.display="none";
        document.getElementById("pvaluey").style.display="";
        break;
    case "outtext":
        document.getElementById("fire").style.display="none";
        document.getElementById("tad").style.display="none";
        document.getElementById("SE").style.display="none";
        document.getElementById("ctcf").style.display="none";
        document.getElementById("chipseq").style.display="none";
        document.getElementById("toggle").style.display="block";
        document.getElementById("pvaluey").style.display="none";
        break;
    case "association":
        break;
    default:
    }
//    document.getElementById("huginform").submit();
}

function update(formname){//submits form based upon formname argument
    document.getElementById(formname).submit();

}

function removeimage(el,id){//turns off a figure - tied to the hide button
    el.parentNode.remove();
    document.getElementById(id).checked=false;
}


function startRead()
{
    // obtain input element through DOM 
    var file = document.getElementById('file').files[0];
    if(file)
	{
        getAsText(file);
    }
}

function getAsText(readFile)
{
	var reader;
	try
	{
        reader = new FileReader();
	}catch(e)
	{
		document.getElementById('output').innerHTML = "Error: seems File API is not supported on your browser";
	    return;
    }
    // Read file into memory as UTF-8      
    reader.readAsText(readFile, "UTF-8");
    // Handle progress, success, and errors
    reader.onprogress = updateProgress;
  reader.onload = loaded;
  reader.onerror = errorHandler;
}

function updateProgress(evt)
{
  if (evt.lengthComputable)
	{
    // evt.loaded and evt.total are ProgressEvent properties
    var loaded = (evt.loaded / evt.total);
    if (loaded < 1)
		{
      // Increase the prog bar length
      // style.width = (loaded * 200) + "px";
			document.getElementById("bar").style.width = (loaded*100) + "%";
    }
  }
}

function loaded(evt)
{
    // Obtain the read file data    
    var fileString = evt.target.result;
    document.getElementById('output').innerHTML = fileString;
	document.getElementById("bar").style.width = 100 + "%";
    document.getElementById("data").value=fileString;
}

function errorHandler(evt)
{
  if(evt.target.error.code == evt.target.error.NOT_READABLE_ERR)
	{
        // The file could not be read
		document.getElementById('output').innerHTML = "Error reading file..."
    }
}

// Function to download data to a file
function download(data, filename, type) {
    var a = document.createElement("a"),
        file = new Blob([data], {type: type});
    if (window.navigator.msSaveOrOpenBlob) // IE10+
        window.navigator.msSaveOrOpenBlob(file, filename);
    else { // Others
        var url = URL.createObjectURL(file);
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        setTimeout(function() {
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);  
        }, 0); 
    }
}

(function (view) {
	"use strict";

	view.URL = view.URL || view.webkitURL;

	if (view.Blob && view.URL) {
		try {
			new Blob;
			return;
		} catch (e) {}
	}

	// Internally we use a BlobBuilder implementation to base Blob off of
	// in order to support older browsers that only have BlobBuilder
	var BlobBuilder = view.BlobBuilder || view.WebKitBlobBuilder || view.MozBlobBuilder || (function(view) {
		var
			  get_class = function(object) {
				return Object.prototype.toString.call(object).match(/^\[object\s(.*)\]$/)[1];
			}
			, FakeBlobBuilder = function BlobBuilder() {
				this.data = [];
			}
			, FakeBlob = function Blob(data, type, encoding) {
				this.data = data;
				this.size = data.length;
				this.type = type;
				this.encoding = encoding;
			}
			, FBB_proto = FakeBlobBuilder.prototype
			, FB_proto = FakeBlob.prototype
			, FileReaderSync = view.FileReaderSync
			, FileException = function(type) {
				this.code = this[this.name = type];
			}
			, file_ex_codes = (
				  "NOT_FOUND_ERR SECURITY_ERR ABORT_ERR NOT_READABLE_ERR ENCODING_ERR "
				+ "NO_MODIFICATION_ALLOWED_ERR INVALID_STATE_ERR SYNTAX_ERR"
			).split(" ")
			, file_ex_code = file_ex_codes.length
			, real_URL = view.URL || view.webkitURL || view
			, real_create_object_URL = real_URL.createObjectURL
			, real_revoke_object_URL = real_URL.revokeObjectURL
			, URL = real_URL
			, btoa = view.btoa
			, atob = view.atob

			, ArrayBuffer = view.ArrayBuffer
			, Uint8Array = view.Uint8Array

			, origin = /^[\w-]+:\/*\[?[\w\.:-]+\]?(?::[0-9]+)?/
		;
		FakeBlob.fake = FB_proto.fake = true;
		while (file_ex_code--) {
			FileException.prototype[file_ex_codes[file_ex_code]] = file_ex_code + 1;
		}
		// Polyfill URL
		if (!real_URL.createObjectURL) {
			URL = view.URL = function(uri) {
				var
					  uri_info = document.createElementNS("http://www.w3.org/1999/xhtml", "a")
					, uri_origin
				;
				uri_info.href = uri;
				if (!("origin" in uri_info)) {
					if (uri_info.protocol.toLowerCase() === "data:") {
						uri_info.origin = null;
					} else {
						uri_origin = uri.match(origin);
						uri_info.origin = uri_origin && uri_origin[1];
					}
				}
				return uri_info;
			};
		}
		URL.createObjectURL = function(blob) {
			var
				  type = blob.type
				, data_URI_header
			;
			if (type === null) {
				type = "application/octet-stream";
			}
			if (blob instanceof FakeBlob) {
				data_URI_header = "data:" + type;
				if (blob.encoding === "base64") {
					return data_URI_header + ";base64," + blob.data;
				} else if (blob.encoding === "URI") {
					return data_URI_header + "," + decodeURIComponent(blob.data);
				} if (btoa) {
					return data_URI_header + ";base64," + btoa(blob.data);
				} else {
					return data_URI_header + "," + encodeURIComponent(blob.data);
				}
			} else if (real_create_object_URL) {
				return real_create_object_URL.call(real_URL, blob);
			}
		};
		URL.revokeObjectURL = function(object_URL) {
			if (object_URL.substring(0, 5) !== "data:" && real_revoke_object_URL) {
				real_revoke_object_URL.call(real_URL, object_URL);
			}
		};
		FBB_proto.append = function(data/*, endings*/) {
			var bb = this.data;
			// decode data to a binary string
			if (Uint8Array && (data instanceof ArrayBuffer || data instanceof Uint8Array)) {
				var
					  str = ""
					, buf = new Uint8Array(data)
					, i = 0
					, buf_len = buf.length
				;
				for (; i < buf_len; i++) {
					str += String.fromCharCode(buf[i]);
				}
				bb.push(str);
			} else if (get_class(data) === "Blob" || get_class(data) === "File") {
				if (FileReaderSync) {
					var fr = new FileReaderSync;
					bb.push(fr.readAsBinaryString(data));
				} else {
					// async FileReader won't work as BlobBuilder is sync
					throw new FileException("NOT_READABLE_ERR");
				}
			} else if (data instanceof FakeBlob) {
				if (data.encoding === "base64" && atob) {
					bb.push(atob(data.data));
				} else if (data.encoding === "URI") {
					bb.push(decodeURIComponent(data.data));
				} else if (data.encoding === "raw") {
					bb.push(data.data);
				}
			} else {
				if (typeof data !== "string") {
					data += ""; // convert unsupported types to strings
				}
				// decode UTF-16 to binary string
				bb.push(unescape(encodeURIComponent(data)));
			}
		};
		FBB_proto.getBlob = function(type) {
			if (!arguments.length) {
				type = null;
			}
			return new FakeBlob(this.data.join(""), type, "raw");
		};
		FBB_proto.toString = function() {
			return "[object BlobBuilder]";
		};
		FB_proto.slice = function(start, end, type) {
			var args = arguments.length;
			if (args < 3) {
				type = null;
			}
			return new FakeBlob(
				  this.data.slice(start, args > 1 ? end : this.data.length)
				, type
				, this.encoding
			);
		};
		FB_proto.toString = function() {
			return "[object Blob]";
		};
		FB_proto.close = function() {
			this.size = 0;
			delete this.data;
		};
		return FakeBlobBuilder;
	}(view));

	view.Blob = function(blobParts, options) {
		var type = options ? (options.type || "") : "";
		var builder = new BlobBuilder();
		if (blobParts) {
			for (var i = 0, len = blobParts.length; i < len; i++) {
				if (Uint8Array && blobParts[i] instanceof Uint8Array) {
					builder.append(blobParts[i].buffer);
				}
				else {
					builder.append(blobParts[i]);
				}
			}
		}
		var blob = builder.getBlob(type);
		if (!blob.slice && blob.webkitSlice) {
			blob.slice = blob.webkitSlice;
		}
		return blob;
	};

	var getPrototypeOf = Object.getPrototypeOf || function(object) {
		return object.__proto__;
	};
	view.Blob.prototype = getPrototypeOf(new view.Blob());
}(typeof self !== "undefined" && self || typeof window !== "undefined" && window || this.content || this));
