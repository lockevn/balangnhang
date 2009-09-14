if(!this.JSON) {
   this.JSON = {
      };
   }
(function() {
   function f(n) {
      return n < 10 ? '0' + n : n; }
   if(typeof Date.prototype.toJSON !== 'function') {
      Date.prototype.toJSON = function(key) {
         return this.getUTCFullYear() + '-' + f(this.getUTCMonth() + 1) + '-' + f(this.getUTCDate()) + 'T' + f(this.getUTCHours()) + ':' + f(this.getUTCMinutes()) + ':' + f(this.getUTCSeconds()) + 'Z'; }; String.prototype.toJSON = Number.prototype.toJSON = Boolean.prototype.toJSON = function(key) {
         return this.valueOf(); }; }
   var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,escapeable=/[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,gap,indent,meta={'\b':'\\b','\t':'\\t','\n':'\\n','\f':'\\f','\r':'\\r','"':'\\"','\\':'\\\\'},rep;function quote(string){escapeable.lastIndex=0;return escapeable.test(string)?'"'+string.replace(escapeable,function(a){var c=meta[a];if(typeof c==='string'){return c;} return'\\u'+('0000'+a.charCodeAt(0).toString(16)).slice(-4);})+'"':'"'+string+'"';} function str(key,holder){var i,k,v,length,mind=gap,partial,value=holder[key];if(value&&typeof value==='object'&&typeof value.toJSON==='function'){value=value.toJSON(key);} if(typeof rep==='function'){value=rep.call(holder,key,value);} switch(typeof value){case'string':return quote(value);case'number':return isFinite(value)?String(value):'null';case'boolean':case'null':return String(value);case'object':if(!value){return'null';} gap+=indent;partial=[];if(typeof value.length==='number'&&!value.propertyIsEnumerable('length')){length=value.length;for(i=0;i
   if(!Array.indexOf) {
      Array.prototype.indexOf = function(o) {
         for(var i = 0; i < this.length; i++)if(this[i] == o)return i;
         return - 1;
         }
      };
   Array.prototype.remove = function(o) {
      for(var i = 0; i < this.length; i++)if(this[i] == o) {
         this.splice(i, 1);
         return;
         }
      return;
      };
   Array.prototype.clone = function() {
      return this.slice(0);
      };
   Array.prototype.each = function(fn) {
      for(var i = 0; i < this.length; i++) {
         if(fn.call(this[i]) == false) {
            return;
            }
         }
      }
   String.prototype.trim = function() {
      return this.replace(/^\s*/,"").replace(/\s*$/,"");
      }
   utility = new Object();
   utility.createCallback = function(callee, func) {
      return {
         trigger : func !== undefined ? func : function() {
            }
         , owner : callee !== undefined ? callee : window}
      };
   utility.triggerCallback = function(callback, args) {
      if(args instanceof Array)return callback.trigger.apply(callback.owner, args);
      elsereturn callback.trigger.call(callback.owner, args);
      };
   utility.throwError = function(message) {
      throw message;
      };
   utility.createCancelArgs = function() {
      return {
         cancel : false}
      };
   utility.cancelEvent = function(cancelArgs) {
      cancelArgs.cancel = true;
      };
   utility.wasCancelled = function(cancelArgs) {
      return cancelArgs.cancel;
      };
   utility.getAbsolutePosition = function(ele) {
      return utility.getRelativePosition(ele, null);
      };
   utility.getRelativePosition = function(node, comp) {
      if(!node) {
         return null;
         }
      var pos;
      pos = {
         left : node.offsetLeft - node.scrollLeft, top : node.offsetTop - node.scrollTop};
      var tempNode;
      if(node !== comp) {
         tempNode = node.offsetParent;
         while(tempNode && (tempNode !== comp)) {
            pos.left += tempNode.offsetLeft - tempNode.scrollLeft;
            pos.top += tempNode.offsetTop - tempNode.scrollTop;
            tempNode = tempNode.offsetParent;
            }
         }
      return pos;
      };
   utility.getRelativeOffset = function(element, offsetElement) {
      var offsetElementPos = utility.getRelativePosition(offsetElement, null);
      element.style.position = "static";
      var elementPos = utility.getRelativePosition(element, null);
      var retval = {
         left : elementPos.left - offsetElementPos.left, top : elementPos.top - offsetElementPos.top};
      element.style.position = "relative";
      return retval;
      };
   utility.setupStyle = function(url, doc) {
      doc = doc ? doc : document;
      if(doc.createStyleSheet) {
         doc.createStyleSheet(url);
         }
      else {
         $(doc.createElement("link")).attr("rel", "stylesheet").attr("type", "text/css").attr("href", url).appendTo(doc.getElementsByTagName("head")[0]);
         }
      };
   utility.getFreeName = function(document, prefix) {
      if(prefix === undefined) {
         prefix = "";
         }
      var id;
      var round = 0;
      while((id = _getFreeName(prefix, round, document)) === undefined) {
         round++;
         }
      return id;
      function _getFreeName(base, round, document) {
         if(round == 0) {
            var id;
            for(var i = 0; i < 26; i++) {
               id = base + String.fromCharCode(i + 65);
               if(round == 0 &&!document.getElementById(id)) {
                  return id;
                  }
               }
            }
         else {
            var id;
            var candidate;
            for(var i = 0; i < 26; i++) {
               id = base + String.fromCharCode(i + 65);
               candidate = _getFreeName(id, round - 1, document);
               if(candidate !== undefined) {
                  return candidate;
                  }
               }
            }
         }
      };
   utility.numberChildElements = function(node) {
      node.contents().each(function() {
         if((this.nodeType == 1) &&!(this.id)) {
            this.id = utility.getFreeName(this.ownerDocument); }
         }
      );
      };
   utility.syncListsUnordered = function(items, itemsRemote, copyPropertiesFunc, createNewItemFunc, isSameItemFunc, isItemProtectedFunc) {
      itemsRemote = itemsRemote.clone();
      var found;
      var itemsToRemove = new Array();
      for(var i = 0; i < items.length; i++) {
         var itemLocal = items[i];
         found = false;
         for(var j = 0; j < itemsRemote.length; j++) {
            var itemRemote = itemsRemote[j];
            if(isSameItemFunc(itemLocal, itemRemote)) {
               found = true;
               copyPropertiesFunc(itemRemote, itemLocal);
               itemsRemote.remove(itemRemote);
               break;
               }
            }
         if(!found) {
            if(!isItemProtectedFunc ||!isItemProtectedFunc(itemLocal)) {
               itemsToRemove.push(itemLocal);
               }
            }
         }
      for(var i = 0; i < itemsRemote.length; i++) {
         var itemRemote = itemsRemote[i];
         var newitem = createNewItemFunc(itemRemote);
         if(newitem != null) {
            items.push(newitem);
            }
         }
      for(var i = 0; i < itemsToRemove.length; i++) {
         items.remove(itemsToRemove[i]);
         }
      };
   utility.createShadowedArray = function(array, insertAtFunc, removeAtFunc) {
      array.push = function() {
         for(var i = 0; i < arguments.length; i++) {
            insertAtFunc.call(array, arguments[i], array.length + i);
            }
         return Array.prototype.push.call(array, arguments);
         };
      array.splice = function() {
         for(var i = arguments[0]; i < arguments[0] + arguments[1]; i++) {
            removeAtFunc.call(array, i);
            }
         for(var i = 2; i < arguments.length; i++) {
            insertAtFunc.call(array, arguments[i], arguments[0] + i - 2);
            }
         return Array.prototype.splice.call(array, arguments);
         };
      array.remove = function() {
         var index = array.indexOf(arguments[0]);
         if(index >- 1) {
            removeAtFunc.call(array, index);
            }
         return Array.prototype.remove.call(array, arguments);
         }
      };
   utility.isInteger = function(str) {
      return!str.length == 0 && utility.strContainsOnly(str.substr(0, 1) == "-" ? str.substr(1) : str, "0123456789");
      };
   utility.strContainsOnly = function(str, checkFor) {
      for(var i = 0; i < str.length; i++) {
         if(checkFor.indexOf(str.charAt(i)) ==- 1) {
            return false;
            }
         }
      return true;
      }
   jQuery.fn.replaceElement = function(orgNode, newNode) {
      for(var i = 0; i < this.length; i++) {
         if(this[i] == orgNode) {
            this[i] = newNode;
            return;
            }
         }
      }
   jQuery.fn.removeMarkerClass = function() {
      return this.removeClass("marker transparent highlight mine red");
      }
   utility.ClearFormattingConstants = {
      removeAll : 1, removeStyle : 2, removeClass : 4, removeId : 8, cleanServer : 16, removeNoAttrSpan : 32, removeEmptySpan : 64}
   utility.clearFormatting = function(level, elements) {
      var all;
      if(elements.is("body")) {
         all = $("*", elements);
         }
      else {
         all = elements.add($("*", elements));
         }
      if(level & utility.ClearFormattingConstants.removeAll) {
         all.each(function() {
            var attrs = this.attributes; for(var i = 0; i < attrs.length; i++) {
               this.removeAttribute(attrs[i].nodeName); }
            }
         )}
      if(level & utility.ClearFormattingConstants.removeId) {
         all.removeAttr("id");
         }
      if(level & utility.ClearFormattingConstants.removeStyle) {
         all.removeAttr("style").filter("font,sub,sup,i,u,s,b,strong,em").each(replaceWithInner);
         }
      if(level & utility.ClearFormattingConstants.removeClass) {
         all.removeClass();
         }
      if(level & utility.ClearFormattingConstants.removeNoAttrSpan) {
         all.filter("span:empty").each(function() {
            var hasAttr = false; var attrs = this.attributes; if(attrs != null) {
               for(var i = 0; i < attrs.length; i++) {
                  if(attrs[i].nodeValue && attrs[i].nodeValue != "") {
                     hasAttr = true; return false; }
                  }
               }
            if(!hasAttr) {
               replaceWithInner.call(this); }
            }
         )}
      if(level & utility.ClearFormattingConstants.removeEmptySpan) {
         all.filter("span").each(function() {
            var me = $(this); if(me.children().length == 0 && me.text().length == 0) {
               replaceWithInner.call(this); }
            }
         )}
      if(level & utility.ClearFormattingConstants.cleanMarker) {
         all.removeMarkerClass();
         }
      if(level & utility.ClearFormattingConstants.cleanServer) {
         var allowed = "h1,h2,h3,table,tbody,tr,td,th,span,img,a,p,pre,ol,ul,li,br,sub,sup";
         all.filter("pre").each(function() {
            var pre = $(this); pre.children("br").each(function() {
               $(this).replaceWith(this.ownerDocument.createTextNode("\n")); }
            ); }
         );
         all.not(allowed).each(function() {
            var me = $(this); var isBold = me.is("strong,b"); var isItalic = me.is("em,i"); var isStrike = me.is("strike"); var isUnderline = me.is("u"); if(isBold || isItalic || isStrike || isUnderline) {
               var span = $(this.ownerDocument.createElement("span")); if(isBold) {
                  span.css("font-weight", "bold"); }
               if(isItalic) {
                  span.css("font-style", "italic"); }
               if(isStrike || isUnderline) {
                  var attr = []; if(isStrike) {
                     attr.push("line-through"); }
                  if(isUnderline) {
                     attr.push("underline"); }
                  span.css("text-decoration", attr.join(" ")); }
               me.replaceWith(span.append(me.contents())); }
            else {
               replaceWithInner.call(this); }
            }
         );
         var allowedAttributes = ['class', 'id', 'style', 'src', 'alt', 'title', 'href'];
         all.filter(allowed).each(function() {
            var attrs = this.attributes; if(attrs != null) {
               for(var i = 0; i < attrs.length; i++) {
                  if(allowedAttributes.indexOf(attrs[i].nodeName.toLowerCase()) ==- 1) {
                     this.removeAttribute(attrs[i].nodeName); }
                  }
               }
            }
         );
         all.filter(allowed).each(function() {
            var me = $(this); if(me.attr("style") !== undefined) {
               var allowedCSSAttr = [ {
                  attr : ['font-weight', 'font-style', 'text-align', 'text-decoration', 'direction']}
               , {
                  is : 'img', attr : ['styleFloat', 'cssFloat', 'width', 'height', 'padding', 'padding-top', 'padding-right', 'padding-bottom', 'padding-left']}
               , {
                  is : 'h1,h2,h3,h4,h5,h6,p,pre', attr : ['margin', 'margin-left']}
               ]; var CSSProp = {
                  }; allowedCSSAttr.each(function() {
                  if(this.is === undefined || me.is(this.is)) {
                     for(var i = 0; i < this.attr.length; i++) {
                        var val = me[0].style[utility.camelCase(this.attr[i], true, "-")]; if(val !== undefined && val != "" && val != "0px" && val != "0pt") {
                           CSSProp[this.attr[i]] = val; }
                        }
                     }
                  }
               ); me.removeAttr("style").css(CSSProp); }
            }
         );
         }
      return;
      function replaceWithInner() {
         $(this).contents().insertBefore(this);
         $(this).remove();
         }
      }
   utility.camelCase = function(str, firstLower, splitString) {
      if(splitString === undefined) {
         splitString = "_";
         }
      var ret = "";
      var components = str.split(splitString);
      components.each(function() {
         ret += this[firstLower ? "toLowerCase" : "toUpperCase"].call(this.substr(0, 1)) + this.substr(1); firstLower = false; }
      );
      return ret;
      }
   utility.createPanelDialog = function(content, pos, eventHandler, title, okLabel, cancelLabel) {
      var cont = utility.createPanel(content, pos, title);
      cont.click(function(event) {
         event.stopPropagation(); }
      );
      var toolbar = utility.createToolbar().css( {
         "margin-top" : 4}
      ).appendTo(cont);
      cont.toolbar = toolbar;
      if(eventHandler !== undefined && okLabel != "-") {
         if(okLabel == null) {
            okLabel = lang.OK;
            }
         utility.createMenuItem(okLabel == lang.CLOSE ? "go-previous.png" : "dialog-apply.png", "ok", okLabel, eventHandler, null).attr("accesskey", "o").addClass("right").appendTo(toolbar);
         }
      if(eventHandler !== undefined && cancelLabel != "-") {
         if(cancelLabel == null) {
            cancelLabel = lang.CANCEL;
            }
         utility.createMenuItem("process-stop.png", "cancel", cancelLabel, eventHandler, null).attr("accesskey", "n").addClass("right").appendTo(toolbar);
         }
      return cont;
      };
   utility.createPanel = function(content, pos, title) {
      var cont = $(document.createElement("div"));
      if(pos !== null) {
         cont.addClass("dialog");
         }
      if(pos) {
         cont.css(pos);
         }
      if(title) {
         var titleRow = $(document.createElement("h3")).text(title);
         cont.append(titleRow);
         }
      cont.append(content);
      return cont;
      };
   jQuery.createEditableLabel = function(beforeEditFunc, afterEditFunc, doc, maxlength) {
      var box = $((doc ? doc : document).createElement("a")).attr("href", "javascript:void(0)").addClass("label").addClass("pointer");
      box.maxlength = maxlength !== undefined ? maxlength : 100;
      box.one("click", box, labelClickHandler);
      return box;
      function labelClickHandler(event) {
         event.stopPropagation();
         var boxParam = event.data;
         var cancelArgs = utility.createCancelArgs();
         cancelArgs.box = boxParam;
         cancelArgs.text = boxParam.text();
         if(beforeEditFunc)utility.triggerCallback(beforeEditFunc, cancelArgs);
         if(!utility.wasCancelled(cancelArgs)) {
            var text = cancelArgs.text;
            var textBox = $((doc ? doc : document).createElement("input")).attr( {
               maxlength : boxParam.maxlength, type : "text", size : text.length > 0 ? text.length : 10}
            ).val(text).addClass("label").bind("keypress", boxParam, textBoxKeyHandler).bind("blur", box, textBoxKeyHandler).click(function(event) {
               event.stopPropagation(); }
            );
            textBox.addClass(boxParam.attr("class"));
            boxParam.replaceWith(textBox);
            textBox[0].focus();
            textBox[0].select();
            }
         else {
            boxParam.one("click", boxParam, labelClickHandler);
            }
         }
      function textBoxKeyHandler(event) {
         event.stopPropagation();
         var boxParam = event.data;
         if(event.type != "keypress" || (event.keyCode == 13 || event.keyCode == 27)) {
            var me = $(this);
            me.replaceWith(boxParam.one("click", boxParam, labelClickHandler));
            var focus = true;
            if(this.value && this.value.length > 0 && (event.type != "keypress" || event.keyCode == 13)) {
               var cancelArgs = utility.createCancelArgs();
               cancelArgs.text = this.value;
               cancelArgs.box = boxParam;
               cancelArgs.editor = me;
               cancelArgs.oldText = boxParam.text();
               if(afterEditFunc) {
                  focus = utility.triggerCallback(afterEditFunc, cancelArgs) !== false;
                  }
               if(!utility.wasCancelled(cancelArgs)) {
                  boxParam.text(cancelArgs.text);
                  }
               }
            if(focus) {
               boxParam.get(0).focus();
               }
            }
         }
      };
   utility.createLink = function(text, handler, obj) {
      var link = $(document.createElement("a")).attr("href", "javascript:void(0)");
      if(text !== undefined) {
         link.text(text);
         }
      if(handler !== undefined) {
         link.bind("click", obj, handler);
         }
      return link;
      }
   utility.createMenu = function(doc) {
      var menu = $(document.createElement("div")).addClass("menu");
      menu.bind("keypress", menu, function(event) {
         if(event.which == 27 &&!event.shiftKey &&!event.altKey &&!event.metaKey) {
            event.data.remove(); }
         }
      );
      return menu;
      };
   utility.createMenuItem = function(url, cmd, label, handler, data) {
      var entry = utility.createLink(label).addClass("item icon");
      if(handler) {
         entry.bind("click", (data ? data : entry), handler);
         }
      if(!data && cmd !== undefined && cmd != null) {
         entry.action = cmd;
         }
      if(url && url != "") {
         entry.css("background-image", "url(" + config.imagePath + url + ")").css("background-repeat", "no-repeat");
         }
      return entry;
      };
   utility.createMenuItemSeparator = function() {
      return $(document.createElement("div")).css("border-top", "1px dashed #666").css("margin", "4px");
      };
   utility.createToolbar = function() {
      return $(document.createElement("div")).addClass("toolbar");
      };
   utility.createButton = function(url, cmd, label, handler, data) {
      var entry = utility.createLink().addClass("icon").css("background-image", "url('" + config.imagePath + url + "')").attr("title", label);
      if(handler) {
         entry.bind("click", (data ? data : entry), handler);
         }
      if(!data && cmd !== undefined && cmd != null) {
         entry.action = cmd;
         }
      return entry;
      };
   utility.createMenuButton = function(text, cmd, label, handler, data) {
      var entry = utility.createLink(text).addClass("item").attr("title", label ? label : "");
      if(handler) {
         entry.bind("click", (data ? data : entry), handler);
         }
      if(!data && cmd) {
         entry.action = cmd;
         }
      return entry;
      };
   utility.createButtonSeparator = function() {
      return $(document.createElement("span"));
      };
   utility.createToolbarSeparator = function() {
      return $(document.createElement("span"));
      };
   jQuery.fn.makeCollapsible = function(container) {
      return this.addClass("collapsible expanded pointer").bind("click", container, function(event) {
         var me = $(this).toggleClass("expanded"); var container = event.data || me.next(); jQuery.fn[me.hasClass("expanded") ? "slideDown" : "slideUp"].call(container); }
      );
      }
   jQuery.fn.disableContextMenu = function() {
      return this.bind("contextmenu", function(event) {
         event.preventDefault(); }
      );
      }
   jQuery.setupPanelShifting = function(lowerPanel, upperPanel, button, shiftContainer, shiftContent) {
      var obj = {
         container : lowerPanel, content : upperPanel, button : button, initialLeftContainer : lowerPanel.get(0).offsetLeft, initialLeftContent : upperPanel.get(0).offsetLeft, shiftContainer : shiftContainer, shiftContent : shiftContent !== undefined ? shiftContent : shiftContainer, handler : function(event) {
            var data = this;
            if(event !== undefined) {
               data = event.data;
               event.stopPropagation();
               }
            var me = $(this);
            var panel1 = data.container;
            var panel2 = data.content;
            panel1.stop().animate( {
               left : me.hasClass("active") ? data.initialLeftContainer : data.initialLeftContainer - shiftContainer}
            );
            panel2.stop().animate( {
               left : me.hasClass("active") ? data.initialLeftContent : data.initialLeftContent + shiftContent}
            );
            me.toggleClass("active");
            }
         , restore : function(callback, immediate) {
            if(callback === undefined) {
               callback = function() {
                  $(this).remove();
                  };
               }
            if(this.button !== undefined) {
               this.button.unbind("click", this.handler);
               this.button.removeClass("active");
               }
            this.container.stop();
            this.content.stop();
            jQuery.fn[immediate ? "css" : "animate"].apply(this.container, [ {
               left : this.initialLeftContainer}
            ]);
            jQuery.fn[immediate ? "css" : "animate"].apply(this.content, [ {
               left : this.initialLeftContent}
            , {
               complete : callback}
            ]);
            if(immediate) {
               this.content.each(callback);
               }
            if(this.callback !== undefined) {
               this.callback.call(this);
               }
            }
         , setup : function() {
            this.button.bind("click", this, this.handler);
            }
         };
      if(button !== undefined) {
         obj.setup();
         }
      else {
         obj.handler();
         }
      return obj;
      };
   utility.setupAlerts = function(container) {
      utility.alertbar = $(document.createElement("div"));
      utility.alertbar.addClass("alertbar").appendTo(container !== undefined ? container : document.body);
      utility.alertSave = window.alert;
      window.alert = function(str) {
         if(str === undefined ||!typeof(str)) {
            str = "undefined";
            }
         var newItem = $(document.createElement("div")).text(str.toString()).hide().addClass("alertitem");
         utility.createLink(lang.REMOVE_ALERT).appendTo(newItem).one("click", newItem, function(e) {
            if(e.data) {
               e.data.fadeOut("normal", function() {
                  e.data.remove()}
               ); }
            }
         );
         newItem.appendTo(utility.alertbar).fadeIn();
         }
      }
   utility.removeAlerts = function() {
      if(utility.alertbar !== undefined) {
         utility.alertbar.children().fadeOut("normal", function() {
            $(this).remove(); }
         );
         }
      }
   utility.destroyAlerts = function() {
      window.alert = utility.alertSave;
      if(utility.alertbar) {
         utility.alertbar.remove();
         utility.alertbar.empty();
         }
      delete utility.alertbar;
      }
   utility.setupButtonHover = function(items, panel, overHook, timeout, delay, closeHandler) {
      if(timeout === undefined) {
         timeout = 500;
         }
      if(delay === undefined) {
         delay = 1000;
         }
      var timer =- 1;
      var showTimer =- 1;
      items.bind("contextmenu", function(event) {
         window.clearTimeout(showTimer); panel.stop(false, true).show(); event.preventDefault(); }
      );
      items.bind(delay ==- 1 ? "click" : "mouseenter", delay, mouseEnterHandler);
      panel.bind("mouseenter", 0, mouseEnterHandler);
      items.add(panel).bind("mouseleave", function() {
         window.clearTimeout(showTimer); window.clearTimeout(timer); timer = window.setTimeout(function() {
            panel.fadeOut("normal", closeHandler); }
         , timeout); }
      );
      return;
      function mouseEnterHandler(event) {
         window.clearTimeout(timer);
         if(!overHook.call(this)) {
            return;
            }
         var func = function() {
            window.clearTimeout(timer);
            panel.stop(false, true).fadeIn("fast");
            };
         if(event.data > 0) {
            showTimer = window.setTimeout(func, event.data);
            }
         else {
            func.call();
            }
         }
      }
   utility.createInput = function(defaultValue, doc) {
      if(doc === undefined) {
         doc = document;
         }
      var removeDefault = function(event) {
         var me = $(this);
         if(me.hasClass("grey")) {
            me.removeClass("grey").val("");
            }
         }
      var field = $(doc.createElement("input")).attr("type", "text").val(defaultValue).one("focus", removeDefault).one("focus", removeDefault).addClass("grey");
      field.val = function(arg) {
         var me = $(this);
         if(arg !== undefined) {
            me.removeClass("grey");
            }
         return me.val(arg);
         }
      return field;
      }
   utility.createCloser = function() {
      return utility.createLink().attr("title", lang.CLOSE).addClass("closer right");
      }
   utility.mozilla = new Object();
   utility.mozilla.setupEnterKeyHandler = function(doc, callback) {
      doc = doc || document;
      doc.addEventListener("keydown", utility.mozilla._keyDownHandler, false);
      doc.addEventListener("DOMNodeInserted", utility.mozilla._domInsertionHandler, false);
      doc.addEventListener("keyup", utility.mozilla._keyUpHandler, false);
      utility.mozilla._callback = callback;
      }
   utility.mozilla._keyDownHandler = function(event) {
      if(event.keyCode == 13 &&!event.altKey &&!event.metaKey &&!event.ctrlKey &&!event.shiftKey) {
         var selection = event.currentTarget.defaultView.getSelection();
         var range = selection.getRangeAt(0);
         var node = range.endContainer;
         if(node.parentNode.firstChild == node && range.startOffset == 0) {
            return;
            }
         if(node.nodeType != 1) {
            node = node.parentNode;
            }
         node.setAttribute("_mb_temp", "true");
         utility.mozilla._waitForInsertion = false;
         utility.mozilla._destElement = node;
         }
      }
   var waitForInsertion;
   utility.mozilla._domInsertionHandler = function(event) {
      var org = utility.mozilla._destElement;
      if(org) {
         var newNode = event.target;
         if(newNode.nodeType == 1 && newNode.getAttribute("_mb_temp") == org.getAttribute("_mb_temp") && utility.mozilla._waitForInsertion == true) {
            newNode.removeAttribute("_mb_temp");
            utility.mozilla._callback(org, newNode);
            org.removeAttribute("_mb_temp");
            utility.mozilla._destElement = null;
            utility.mozilla._waitForInsertion = false;
            }
         if(!newNode.nextSibling)return;
         var body = newNode.ownerDocument.body;
         var temp = org;
         do {
            if(newNode.nextSibling == temp) {
               newNode.removeAttribute("_mb_temp");
               utility.mozilla._callback(temp, newNode);
               utility.mozilla._waitForInsertion = true;
               break;
               }
            }
         while((temp = temp.parentNode) && temp != body)}
      }
   utility.mozilla._keyUpHandler = function(event) {
      if(event.keyCode == 13 && utility.mozilla._destElement) {
         utility.mozilla._destElement.removeAttribute("_mb_temp");
         utility.mozilla._destElement = null;
         utility.mozilla._waitForInsertion = false;
         }
      }
   utility.mozilla.removeEnterKeyHandler = function(doc) {
      doc = doc || document;
      doc.removeEventListener("keydown", utility.mozilla._keyDownHandler, false);
      doc.removeEventListener("DOMNodeInserted", utility.mozilla._domInsertionHandler, false);
      doc.removeEventListener("keyup", utility.mozilla._keyUpHandler, false);
      }
   jQuery.fn.takeSelection = function() {
      var selection = jQuery.getSelection( {
         context : this.get(0).ownerDocument}
      );
      while(selection.is("tbody,tr,td,th")) {
         selection = selection.contents(":first");
         }
      this.insertBefore(selection.filter(":first"));
      if(selection.length == 1 && selection.is("span") && selection.attr("id") == "") {
         var sel = selection;
         selection = selection.contents();
         sel.remove();
         }
      return this.append(selection);
      };
   jQuery.fn.insertNearSelection = function(before) {
      var selection = jQuery.getSelection( {
         context : this.get(0).ownerDocument, collapse : before}
      ).filter(":first");
      while(selection.is("tbody,tr,td,th")) {
         selection = selection.contents(":first");
         }
      var ret = this.insertBefore(selection);
      selection.remove();
      return ret;
      }
   jQuery.fn.selectNodes = function(options) {
      if(this.length == 0) {
         return false;
         }
      var document = this.get(0).ownerDocument;
      var ELEMENT_NODE = 1;
      if(jQuery.browser.msie) {
         var range;
         this.each(function() {
            if(this.nodeType == ELEMENT_NODE) {
               if(!range) {
                  range = this.ownerDocument.body.createTextRange(); range.moveToElementText(this); }
               else {
                  var compRange = this.ownerDocument.body.createTextRange(); compRange.moveToElementText(this); if(range.compareEndPoints("StartToStart", compRange) == 1) {
                     range.setEndPoint("StartToStart", compRange); }
                  if(range.compareEndPoints("EndToEnd", compRange) ==- 1) {
                     range.setEndPoint("EndToEnd", compRange); }
                  }
               }
            }
         );
         if(range) {
            if(options !== undefined && options.collapse !== undefined) {
               range.collapse(options.collapse);
               }
            range.select();
            }
         }
      else {
         var selection = document.defaultView.getSelection();
         if(!selection) {
            return;
            }
         selection.removeAllRanges();
         var elements = this.getFirstAndLast();
         if(!elements.first) {
            return;
            }
         var firstRange = elements.first.ownerDocument.createRange();
         firstRange.selectNodeContents(elements.first);
         var lastRange = elements.last.ownerDocument.createRange();
         lastRange.selectNodeContents(elements.last);
         var range = elements.first.ownerDocument.createRange();
         range.setStart(elements.first, firstRange.startOffset);
         range.setEnd(elements.last, lastRange.endOffset);
         if(elements.last == elements.first && elements.last.tagName.toLowerCase() == "br") {
            range.setStartBefore(elements.first);
            range.setEndAfter(elements.first);
            }
         if(options !== undefined && options.collapse !== undefined) {
            range.collapse(options.collapse);
            }
         selection.addRange(range);
         }
      return this;
      };
   jQuery.getSelection = function(options) {
      var isIE = jQuery.browser.msie;
      var TEXT_NODE = 3;
      var ELEMENT_NODE = 1;
      options = jQuery.extend( {
         context : document, disableSurrounding : false, returnNullIfCollapsed : false, allowTextNodes : false}
      , options !== undefined ? options : {
         }
      );
      var selection;
      if(isIE) {
         options.context.parentWindow.focus();
         selection = options.context.selection;
         }
      else {
         options.context.defaultView.focus();
         selection = options.context.defaultView.getSelection();
         }
      var collapsed = (selection.isCollapsed || selection.type == "None");
      if(collapsed && options.returnNullIfCollapsed) {
         return null;
         }
      var nodes = [];
      var ranges = [];
      if(isIE) {
         ranges.push(selection.createRange());
         }
      else {
         for(var i = 0; i < selection.rangeCount; i++) {
            ranges.push(selection.getRangeAt(i));
            }
         }
      for(var i = 0; i < ranges.length; i++) {
         var range = ranges[i];
         if(options.collapse !== undefined) {
            range.collapse(options.collapse);
            collapsed = true;
            }
         if(isIE &&!range.parentElement) {
            for(var j = 0; j < range.length; j++) {
               nodes.push(range.item(j));
               }
            continue;
            }
         var candidates = [];
         var parent = isIE ? range.parentElement() : range.commonAncestorContainer;
         var last = isIE ? utility.getOuter(range, false) : range.endContainer;
         var first = isIE ? (selection.type == "None" ? last : utility.getOuter(range, true)) : range.startContainer;
         if(first == first.ownerDocument.body && collapsed) {
            first = first.ownerDocument.createTextNode("");
            first.ownerDocument.body.appendChild(first);
            }
         if(!options.disableSurrounding && ((first.nodeType == TEXT_NODE && first == last) || (first.nodeType == TEXT_NODE && collapsed))) {
            var temp;
            temp = surroundContents(first, range);
            nodes.push(temp);
            continue;
            }
         var ignoreFirst;
         if(first.nodeType == TEXT_NODE) {
            if(!options.disableSurrounding) {
               var collapsed = false;
               if(isIE) {
                  var firstRange = options.context.body.createTextRange();
                  utility.moveToElementText(firstRange, first);
                  firstRange.setEndPoint("StartToStart", range);
                  collapsed = firstRange.text.length == 0;
                  }
               else {
                  var firstRange = options.context.createRange();
                  firstRange.selectNode(first);
                  firstRange.setStart(first, range.startOffset);
                  collapsed = firstRange.collapsed;
                  }
               if(!collapsed) {
                  first = surroundContents(first, firstRange);
                  }
               else {
                  ignoreFirst = true;
                  }
               }
            }
         else if(!isIE) {
            if(first.childNodes.length > range.startOffset) {
               first = first.childNodes[range.startOffset];
               }
            }
         var firstTree = [first];
         var temp = first;
         if(first != parent) {
            while((temp = temp.parentNode) && temp != parent) {
               firstTree.unshift(temp);
               }
            }
         var ignoreLast;
         if(last.nodeType == TEXT_NODE) {
            if(!options.disableSurrounding) {
               var collapsed = false;
               if(isIE) {
                  var lastRange = options.context.body.createTextRange();
                  utility.moveToElementText(lastRange, last);
                  lastRange.setEndPoint("EndToEnd", range);
                  collapsed = lastRange.text.length == 0;
                  }
               else {
                  var lastRange = options.context.createRange();
                  lastRange.selectNode(last);
                  lastRange.setEnd(last, range.endOffset);
                  collapsed = lastRange.collapsed;
                  }
               if(!collapsed) {
                  last = surroundContents(last, lastRange);
                  }
               else {
                  ignoreLast = true;
                  }
               }
            }
         else if(!isIE) {
            ignoreLast = last.childNodes.length != range.endOffset;
            last = ignoreLast ? last.childNodes[range.endOffset] : (last.lastChild ? last.lastChild : last);
            }
         var lastTree = [last];
         temp = last;
         if(last != parent) {
            while((temp = temp.parentNode) && temp != parent) {
               lastTree.unshift(temp);
               }
            }
         var commonLength = firstTree.length > lastTree.length ? lastTree.length : firstTree.length;
         var stopIndex =- 1;
         var firstElement, lastElement;
         for(var j = 0; j < commonLength; j++) {
            firstElement = firstTree[j];
            lastElement = lastTree[j];
            if(firstElement != lastElement) {
               stopIndex = j;
               break;
               }
            }
         if(!ignoreFirst) {
            candidates.push(first);
            }
         if(stopIndex >- 1) {
            for(var j = firstTree.length - 1; j > stopIndex; j--) {
               var treeNode = firstTree[j];
               if(options.disableSurrounding) {
                  candidates.push(treeNode);
                  }
               while(treeNode = treeNode.nextSibling) {
                  candidates.push(treeNode);
                  }
               }
            if(options.disableSurrounding) {
               candidates.push(firstElement);
               }
            while((firstElement = firstElement.nextSibling) && (firstElement != lastElement)) {
               if(firstElement.nodeType != TEXT_NODE || options.allowTextNodes ||!options.disableSurrounding) {
                  candidates.push(firstElement);
                  }
               }
            if(options.disableSurrounding && firstElement != null) {
               candidates.push(firstElement);
               }
            for(var j = stopIndex + 1; j < lastTree.length; j++) {
               var treeNode = lastTree[j];
               var pos = candidates.length;
               if(options.disableSurrounding) {
                  candidates.splice(pos, 0, treeNode);
                  }
               while(treeNode = treeNode.previousSibling) {
                  candidates.splice(pos, 0, treeNode);
                  }
               }
            if(!ignoreLast) {
               candidates.push(last);
               }
            }
         for(var j = 0; j < candidates.length; j++) {
            var candidate = candidates[j];
            if(candidate.nodeType == TEXT_NODE) {
               if(candidate.nodeValue.length > 0 || candidates.length == 1) {
                  if(!options.disableSurrounding) {
                     nodes.push(surroundContents(candidate));
                     }
                  else if(options.allowTextNodes) {
                     nodes.push(candidate);
                     }
                  else {
                     nodes.push(candidate.parentNode);
                     }
                  }
               }
            else {
               nodes.push(candidate);
               }
            }
         }
      if(!options.disableSurrounding) {
         if(isIE) {
            selection.empty();
            }
         else {
            selection.removeAllRanges();
            }
         }
      return jQuery(nodes);
      function surroundContents(node, range) {
         var spanNode = node.ownerDocument.createElement("span");
         var before = node;
         var parent = node.parentNode;
         if(!range && parent.childNodes.length == 1 && parent.tagName.toLowerCase() == "span") {
            return parent;
            }
         if(range) {
            if(isIE) {
               var length = range.text.length;
               var nodeLength = node.nodeValue.length;
               if(length == nodeLength && node.parentNode.tagName.toLowerCase() == "span" && node.parentNode.childNodes.length == 1) {
                  return node.parentNode;
                  }
               var tempRange = node.ownerDocument.body.createTextRange();
               utility.moveToElementText(tempRange, node);
               tempRange.setEndPoint("EndToStart", range);
               var splitAt = tempRange.text.length;
               if(splitAt > 0) {
                  if(splitAt < nodeLength) {
                     before = node = node.splitText(splitAt);
                     }
                  else {
                     before = node.nextSibling;
                     node = null;
                     }
                  }
               if(splitAt < nodeLength && (length + splitAt) < nodeLength) {
                  before = node = node.splitText(length).previousSibling;
                  }
               }
            else {
               if(node.nodeType == TEXT_NODE && node.parentNode.childNodes.length == 1 && range.startOffset == 0 && range.endOffset == node.nodeValue.length && node.parentNode.tagName.toLowerCase() == "span") {
                  return node.parentNode;
                  }
               range.surroundContents(spanNode);
               return spanNode;
               }
            }
         parent.insertBefore(spanNode, before);
         if(node) {
            spanNode.appendChild(node);
            }
         return spanNode;
         }
      };
   jQuery.storeSelection = function(options) {
      var isIE = jQuery.browser.msie;
      var TEXT_NODE = 3;
      var textOffset = 0;
      var selection, range, start;
      if(isIE) {
         options.context.parentWindow.focus();
         selection = options.context.selection;
         range = selection.createRange();
         range.collapse(true);
         var obj = {
            };
         start = utility.getOuter(range, true, obj);
         textOffset = obj.offset;
         }
      else {
         options.context.defaultView.focus();
         selection = options.context.defaultView.getSelection();
         if(!selection) {
            return undefined;
            }
         try {
            range = selection.getRangeAt(0);
            }
         catch(ex) {
            return undefined;
            }
         range.collapse(true);
         start = range.endContainer;
         }
      var offset =- 1;
      if(start.nodeType == 3) {
         if(!isIE) {
            textOffset = range.startOffset;
            }
         var parent = start.parentNode;
         for(var i = 0; i < parent.childNodes.length; i++) {
            if(parent.childNodes[i] === start) {
               offset = i;
               break;
               }
            }
         start = parent;
         }
      else if(!isIE) {
         var candidate = start.childNodes.item(range.startOffset);
         if(candidate && candidate.nodeType == 1) {
            start = candidate;
            }
         else {
            }
         if(!start) {
            return undefined;
            }
         textOffset =- 1;
         }
      return {
         id : start.getAttribute('id'), offset : offset, textOffset : textOffset};
      }
   jQuery.restoreSelection = function(obj, options) {
      if(!obj.id) {
         return false;
         }
      var isIE = jQuery.browser.msie;
      var begin = options.context.getElementById(obj.id);
      if(!begin) {
         return false;
         }
      if(obj.offset >- 1 &&!isIE) {
         if(begin.childNodes.length > obj.offset) {
            begin = begin.childNodes[obj.offset];
            }
         else {
            return;
            }
         }
      var range;
      try {
         if(isIE) {
            range = options.context.body.createTextRange();
            range.moveToElementText(begin);
            if(obj.textOffset >- 1) {
               range.moveStart("character", obj.textOffset);
               }
            range.collapse(true);
            range.select();
            }
         else {
            var selection = options.context.defaultView.getSelection();
            selection.removeAllRanges();
            range = options.context.createRange();
            range.selectNodeContents(begin);
            if(begin.nodeType == 1 && begin.tagName.toLowerCase() == "br") {
               range.setStartBefore(begin);
               }
            if(obj.textOffset >- 1) {
               range.setStart(begin, obj.textOffset);
               }
            range.collapse(true);
            selection.addRange(range);
            }
         }
      catch(ex) {
         return false;
         }
      return true;
      }
   utility.moveToElementText = function(range, textNode) {
      var TEXT_NODE = 3;
      var ELEMENT_NODE = 1;
      if(textNode.nodeType == ELEMENT_NODE) {
         range.moveToElementText(textNode);
         return;
         }
      var parent = textNode.parentNode;
      range.moveToElementText(parent);
      var offset = 0;
      for(var i = 0; i < parent.childNodes.length; i++) {
         var node = parent.childNodes[i];
         if(node == textNode) {
            range.moveStart("character", 1);
            range.moveStart("character", - 1);
            range.collapse(true);
            range.moveEnd("character", textNode.length);
            return;
            }
         if(node.nodeType == TEXT_NODE) {
            offset = node.length;
            range.moveStart("character", offset);
            var chr = "";
            while((chr = range.text.substr(0, 1)) && (chr == "\n" || chr == "\r")) {
               range.moveStart("word", 1);
               }
            }
         else {
            var tempRange = node.ownerDocument.body.createTextRange();
            tempRange.moveToElementText(node);
            range.setEndPoint("StartToEnd", tempRange);
            }
         }
      }
   utility.getOuter = function(range, first, receiver) {
      var TEXT_NODE = 3;
      var dup = range.duplicate();
      dup.collapse(first);
      var parent = dup.parentElement();
      var wholeRange = parent.ownerDocument.body.createTextRange();
      wholeRange.moveToElementText(parent);
      wholeRange.setEndPoint("EndToStart", dup);
      var text = wholeRange.text;
      text = utility.removeBreaks(text);
      var offset = text.length;
      if(receiver !== undefined) {
         receiver.offset = offset;
         }
      var node = parent.firstChild;
      while(node) {
         if(node.nodeType == TEXT_NODE) {
            offset -= node.nodeValue.length;
            if(first ? (offset < 0) : (offset <= 0)) {
               return node;
               }
            }
         else {
            if(node.firstChild) {
               node = node.firstChild;
               continue;
               }
            }
         if(node.nextSibling) {
            node = node.nextSibling;
            continue;
            }
         while(node = node.parentNode) {
            if(node == parent) {
               return parent;
               }
            if(node.nextSibling) {
               node = node.nextSibling;
               break;
               }
            }
         }
      return parent;
      }
   utility.removeBreaks = function(str) {
      var newStr = "";
      for(var i = 0; i < str.length; i++) {
         if((str.charAt(i) != "\r") && (str.charAt(i) != "\n")) {
            newStr = newStr + str.charAt(i);
            }
         }
      return newStr;
      }
   jQuery.fn.getFirstAndLast = function() {
      var first, last;
      this.each(function() {
         var parent = this; while(parent != this.ownerDocument.body && (parent = parent.parentNode)); if(parent) {
            if(!first) {
               first = last = this; }
            else {
               last = this; }
            }
         }
      )return {
         first : first, last : last};
      };
   jQuery.fn.containsVisibleElements = function() {
      for(var j = 0; j < this.length; j++) {
         var parent = this[j];
         while((parent = parent.parentNode) && parent != this[j].ownerDocument.body);
         if(parent) {
            return true;
            }
         }
      return false;
      };
   jQuery.fn.getRangeHeight = function() {
      var outerElements = this.getFirstAndLast();
      var topX = utility.getAbsolutePosition(outerElements.first).top;
      var bottomX = utility.getAbsolutePosition(outerElements.last).top + outerElements.last.offsetHeight;
      return bottomX - topX;
      };
   jQuery.fn.toRangeString = function() {
      var elements = this.get();
      var nodes = [];
      if(elements.length > 0) {
         for(var i = 0; i < elements.length; i++) {
            var ele = elements[i];
            var parent = ele;
            while((parent = parent.parentNode) && parent != ele.ownerDocument.body);
            if(parent) {
               nodes.push(getNodePathFromNode(ele));
               }
            }
         return JSON.stringify(nodes);
         }
      else {
         return null;
         }
      function getNodePathFromNode(domNode) {
         var path = new Array();
         if(!domNode.parentNode)return;
         do {
            var inParent =- 1;
            for(var i = 0; i < domNode.parentNode.childNodes.length; i++) {
               if(domNode.parentNode.childNodes[i] == domNode) {
                  inParent = i;
                  break;
                  }
               }
            path.unshift( {
               id : domNode.id, pos : inParent, tag : domNode.tagName}
            );
            }
         while((domNode = domNode.parentNode) && domNode.parentNode && domNode != domNode.ownerDocument.body)return path;
         }
      };
   jQuery.fn.focusFirstEditable = function() {
      var candidate = $("(a,input,button,select,textarea):first", this);
      if(candidate.length == 1) {
         candidate.get(0).focus();
         }
      return this;
      }
   jQuery.fromRangeString = function(stringRange, context) {
      if(stringRange == null) {
         return null;
         }
      var nodes = JSON.parse(stringRange);
      var range = new Array();
      for(var i = 0; i < nodes.length; i++) {
         var nodePath = nodes[i];
         var domNode = getNodeFromNodePath(nodePath, context);
         if(domNode)range.push(domNode);
         }
      return jQuery(range);
      function getNodeFromNodePath(nodePath, context) {
         context = context.body ? context.body : context;
         for(var i = nodePath.length - 1; i >- 1; i--) {
            var nodeObj = nodePath[i];
            var levelNode = (nodeObj.id != "") ? context.ownerDocument.getElementById(nodeObj.id) : null;
            if(!levelNode && i == 0)levelNode = context;
            if(levelNode) {
               for(var j = i + 1; j < nodePath.length; j++) {
                  nodeObj = nodePath[j];
                  if(levelNode.childNodes.length > nodeObj.pos && levelNode.childNodes[nodeObj.pos].nodeType == 1) {
                     levelNode = levelNode.childNodes.length > nodeObj.pos ? levelNode.childNodes[nodeObj.pos] : levelNode;
                     }
                  else {
                     var subCol = levelNode.getElementsByTagName(nodeObj.tag)if(!subCol ||!subCol[0])break;
                     levelNode = subCol[0];
                     }
                  }
               return levelNode == context ? null : levelNode;
               }
            }
         return null;
         }
      };
   jQuery.getSelectionText = function(context) {
      var doc = context ? context : document;
      if(jQuery.browser.msie)return doc.selection.createRange().text;
      elsereturn doc.defaultView.getSelection().toString();
      };
   jQuery.clearSelection = function(context) {
      var doc = context ? context : document;
      if(jQuery.browser.msie) {
         doc.selection.empty();
         }
      else {
         doc.defaultView.getSelection().removeAllRanges();
         }
      }
   jQuery.hasSelection = function(context) {
      var doc = context ? context : document;
      if(jQuery.browser.msie) {
         return doc.selection.type != 'None';
         }
      else {
         var sel = doc.defaultView.getSelection();
         return sel != null &&!sel.isCollapsed;
         }
      }
   jQuery.fn.htmlWithStructure = function(filterFunction) {
      var doc = this.get(0).ownerDocument;
      var bodyNode = doc.body;
      var bodyClone;
      if(jQuery.browser.msie) {
         var html = "<div>" + $(doc.body).html() + "</div>";
         bodyClone = $(html, doc).get(0);
         }
      else {
         bodyClone = $(bodyNode).clone().get(0);
         }
      var validNodes = [];
      this.each(function() {
         var node = this; addAll(node); function addAll(node) {
            if(node.nodeType == 1) {
               for(var i = 0; i < node.childNodes.length; i++) {
                  validNodes.push(node.childNodes[i]); addAll(node.childNodes[i]); }
               }
            }
         while(node != node.ownerDocument.body) {
            validNodes.push(node); node = node.parentNode; }
         validNodes.push(node); }
      )deleteUnmatched(bodyNode, bodyClone);
      if(filterFunction !== undefined) {
         $("*", bodyClone).each(filterFunction);
         }
      return $(bodyClone).html();
      function deleteUnmatched(bodyNode, bodyClone) {
         for(var i = bodyClone.childNodes.length - 1; i >= 0; i--) {
            var comparison = bodyNode.childNodes[i];
            var node = bodyClone.childNodes[i];
            if((comparison !== undefined) && (node !== undefined)) {
               if(comparison.nodeType == 1 && node.nodeType == 1) {
                  deleteUnmatched(comparison, node);
                  }
               if(validNodes.indexOf(comparison) ==- 1) {
                  node.parentNode.removeChild(node);
                  }
               }
            }
         }
      }
   jQuery.balloon = {
      FOCUS_CLASS : "balloonFocus", IMAGE_BODY : "balloon-body.gif", IMAGE_TOP : "balloon-top.gif", IMAGE_BOTTOM : "balloon-bottom.gif"};
   jQuery.balloon.BalloonPositionConstants = {
      topLeft : 0, topMiddle : 1, topRight : 2, bottomLeft : 3, bottomMiddle : 4, bottomRight : 5, centerLeft : 6, centerMiddle : 7, centerRight : 8};
   jQuery.showBalloon = function(element, content, position, closeHandler, store, alias, after, initialCheck) {
      var balloonBody = $(document.createElement("div"));
      if(content instanceof jQuery) {
         balloonBody.append(content);
         }
      else {
         balloonBody.html(content);
         }
      if(store && alias) {
         if(store[alias] == true) {
            return handleClose.apply(element[0], [closeHandler, after]);
            }
         var balloonId;
         var checkbox = $(document.createElement("input")).attr( {
            "type" : "checkbox", "id" : balloonId = utility.getFreeName(document)}
         ).appendTo(balloonBody).bind("click", function(event) {
            if(this.checked) {
               store[alias] = true; }
            else {
               delete store[alias]; }
            }
         );
         if(initialCheck) {
            checkbox.get(0).checked = true;
            store[alias] = true;
            }
         var label = $(document.createElement("label")).attr( {
            "for" : balloonId}
         ).text(lang.DO_NOT_SHOW_AGAIN).insertAfter(checkbox);
         }
      var balloon = balloonBody.addClass("balloon").click(function(event) {
         event.stopPropagation(); }
      );
      element.addClass(jQuery.balloon.FOCUS_CLASS);
      balloon.element = element;
      balloon.remove = function() {
         this.element.removeClass(jQuery.balloon.FOCUS_CLASS);
         jQuery.fn.fadeOut.apply(this, [200, function() {
            $(this).remove(); }
         ]);
         };
      $(document.createElement("img")).attr( {
         src : config.imagePath + "dialog-information-small.png"}
      ).css( {
         "float" : "left", "padding-right" : 3}
      ).prependTo(balloonBody);
      utility.createButton("document-close-small.png", null, lang.CLOSE).css( {
         "float" : "right", "width" : 16, "height" : 16}
      ).bind("click", [element[0], closeHandler, after], cancelXHandler).prependTo(balloonBody);
      var dest = element.closest("div.mb-cont");
      if(dest.length == 0) {
         dest = elemen.parent();
         }
      balloon.appendTo(dest);
      var balloonPos = {
         width : 200}
      var elementDOM = element.get(0);
      var relPos = utility.getRelativePosition(elementDOM, dest.get(0));
      var elemPos = {
         l : relPos.left, t : relPos.top};
      elemPos.r = elemPos.l + elementDOM.offsetWidth;
      elemPos.b = elemPos.t + elementDOM.offsetHeight;
      var parentH = dest.height();
      var parentW = dest.width();
      var balloonWidth = 216;
      switch(position) {
         case jQuery.balloon.BalloonPositionConstants.topLeft : balloonPos.right = parentW - elemPos.l + 5;
         balloonPos.bottom = parentH - elemPos.t + 5;
         break;
         case jQuery.balloon.BalloonPositionConstants.topMiddle : balloonPos.right = parentW - elemPos.l - Math.round(elementDOM.offsetWidth / 2 + balloonWidth / 2);
         balloonPos.bottom = parentH - elemPos.t + 5;
         break;
         case jQuery.balloon.BalloonPositionConstants.topRight : balloonPos.right = parentW - elemPos.r - balloonWidth;
         balloonPos.bottom = parentH - elemPos.t + 5;
         break;
         case jQuery.balloon.BalloonPositionConstants.bottomLeft : balloonPos.right = parentW - elemPos.l + 5;
         balloonPos.top = elemPos.b + 5;
         break;
         case jQuery.balloon.BalloonPositionConstants.bottomMiddle : balloonPos.right = parentW - elemPos.l - Math.round(elementDOM.offsetWidth / 2 + balloonWidth / 2);
         balloonPos.top = elemPos.b + 5;
         break;
         case jQuery.balloon.BalloonPositionConstants.bottomRight : balloonPos.right = parentW - elemPos.r - balloonWidth;
         balloonPos.top = elemPos.b + 5;
         break;
         case jQuery.balloon.BalloonPositionConstants.centerLeft : balloonPos.right = parentW - elemPos.l + 5;
         balloonPos.bottom = parentH - elemPos.t + 5 - elementDOM.offsetHeight;
         break;
         case jQuery.balloon.BalloonPositionConstants.centerRight : balloonPos.right = parentW - elemPos.r - balloonWidth;
         balloonPos.bottom = parentH - elemPos.t - elementDOM.offsetHeight + 5;
         break;
         }
      balloon.hide().css(balloonPos);
      balloon.fadeIn();
      return balloon;
      function cancelXHandler(event) {
         $(event.data[0]).removeClass(jQuery.balloon.FOCUS_CLASS);
         handleClose.apply(event.data[0], event.data.slice(1, 3));
         $(this).parent().fadeOut(300, function() {
            $(this).remove(); }
         );
         }
      function handleClose(closeHandler, after) {
         if(closeHandler !== undefined && closeHandler != null) {
            closeHandler.call(this, null);
            }
         if(after !== undefined) {
            if(after instanceof Array) {
               var func;
               if(func = after.shift()) {
                  return func.call(this, after);
                  }
               }
            }
         return null;
         }
      };
   jQuery.ui || (function($) {
      var _remove = $.fn.remove, isFF2 = $.browser.mozilla && (parseFloat($.browser.version) < 1.9); $.ui = {
         version : "1.7", plugin : {
            add : function(module, option, set) {
               var proto = $.ui[module].prototype; for(var i in set) {
                  proto.plugins[i] = proto.plugins[i] || []; proto.plugins[i].push([option, set[i]]); }
               }
            , call : function(instance, name, args) {
               var set = instance.plugins[name]; if(!set ||!instance.element[0].parentNode) {
                  return; }
               for(var i = 0; i < set.length; i++) {
                  if(instance.options[set[i][0]]) {
                     set[i][1].apply(instance.element, args); }
                  }
               }
            }
         , contains : function(a, b) {
            return document.compareDocumentPosition ? a.compareDocumentPosition(b) & 16 : a !== b && a.contains(b); }
         , hasScroll : function(el, a) {
            if($(el).css('overflow') == 'hidden') {
               return false; }
            var scroll = (a && a == 'left') ? 'scrollLeft':'scrollTop', has = false; if(el[scroll] > 0) {
               return true; }
            el[scroll] = 1; has = (el[scroll] > 0); el[scroll] = 0; return has; }
         , isOverAxis : function(x, reference, size) {
            return(x > reference) && (x < (reference + size)); }
         , isOver : function(y, x, top, left, height, width) {
            return $.ui.isOverAxis(y, top, height) && $.ui.isOverAxis(x, left, width); }
         , keyCode : {
            BACKSPACE : 8, CAPS_LOCK : 20, COMMA : 188, CONTROL : 17, DELETE : 46, DOWN : 40, END : 35, ENTER : 13, ESCAPE : 27, HOME : 36, INSERT : 45, LEFT : 37, NUMPAD_ADD : 107, NUMPAD_DECIMAL : 110, NUMPAD_DIVIDE : 111, NUMPAD_ENTER : 108, NUMPAD_MULTIPLY : 106, NUMPAD_SUBTRACT : 109, PAGE_DOWN : 34, PAGE_UP : 33, PERIOD : 190, RIGHT : 39, SHIFT : 16, SPACE : 32, TAB : 9, UP : 38}
         }; if(isFF2) {
         var attr = $.attr, removeAttr = $.fn.removeAttr, ariaNS = "http://www.w3.org/2005/07/aaa", ariaState = /^aria-/,ariaRole=/^wairole:/;$.attr=function(elem,name,value){var set=value!==undefined;return(name=='role'?(set?attr.call(this,elem,name,"wairole:"+value):(attr.apply(this,arguments)||"").replace(ariaRole,"")):(ariaState.test(name)?(set?elem.setAttributeNS(ariaNS,name.replace(ariaState,"aaa:"),value):attr.call(this,elem,name.replace(ariaState,"aaa:"))):attr.apply(this,arguments)));};$.fn.removeAttr=function(name){return(ariaState.test(name)?this.each(function(){this.removeAttributeNS(ariaNS,name.replace(ariaState,""));}):removeAttr.call(this,name));};} $.fn.extend({remove:function(){$("*",this).add(this).each(function(){$(this).triggerHandler("remove");});return _remove.apply(this,arguments);},enableSelection:function(){return this.attr('unselectable','off').css('MozUserSelect','').unbind('selectstart.ui');},disableSelection:function(){return this.attr('unselectable','on').css('MozUserSelect','none').bind('selectstart.ui',function(){return false;});},scrollParent:function(){var scrollParent;if(($.browser.msie&&(/(static|relative)/).test(this.css('position')))||(/absolute/).test(this.css('position'))){scrollParent=this.parents().filter(function(){return(/(relative|absolute|fixed)/).test($.curCSS(this,'position',1))&&(/(auto|scroll)/).test($.curCSS(this,'overflow',1)+$.curCSS(this,'overflow-y',1)+$.curCSS(this,'overflow-x',1));}).eq(0);}else{scrollParent=this.parents().filter(function(){return(/(auto|scroll)/).test($.curCSS(this,'overflow',1)+$.curCSS(this,'overflow-y',1)+$.curCSS(this,'overflow-x',1));}).eq(0);} return(/fixed/).test(this.css('position'))||!scrollParent.length?$(document):scrollParent;}});$.extend($.expr[':'],{data:function(elem,i,match){return!!$.data(elem,match[3]);},focusable:function(element){var nodeName=element.nodeName.toLowerCase(),tabIndex=$.attr(element,'tabindex');return(/input|select|textarea|button|object/.test(nodeName)?!element.disabled:'a'==nodeName||'area'==nodeName?element.href||!isNaN(tabIndex):!isNaN(tabIndex))&&!$(element)['area'==nodeName?'parents':'closest'](':hidden').length;},tabbable:function(element){var tabIndex=$.attr(element,'tabindex');return(isNaN(tabIndex)||tabIndex>=0)&&$(element).is(':focusable');}});function getter(namespace,plugin,method,args){function getMethods(type){var methods=$[namespace][plugin][type]||[];return(typeof methods=='string'?methods.split(/,?\s+/):methods);} var methods=getMethods('getter');if(args.length==1&&typeof args[0]=='string'){methods=methods.concat(getMethods('getterSetter'));} return($.inArray(method,methods)!=-1);} $.widget=function(name,prototype){var namespace=name.split(".")[0];name=name.split(".")[1];$.fn[name]=function(options){var isMethodCall=(typeof options=='string'),args=Array.prototype.slice.call(arguments,1);if(isMethodCall&&options.substring(0,1)=='_'){return this;} if(isMethodCall&&getter(namespace,name,options,args)){var instance=$.data(this[0],name);return(instance?instance[options].apply(instance,args):undefined);} return this.each(function(){var instance=$.data(this,name);(!instance&&!isMethodCall&&$.data(this,name,new $[namespace][name](this,options))._init());(instance&&isMethodCall&&$.isFunction(instance[options])&&instance[options].apply(instance,args));});};$[namespace]=$[namespace]||{};$[namespace][name]=function(element,options){var self=this;this.namespace=namespace;this.widgetName=name;this.widgetEventPrefix=$[namespace][name].eventPrefix||name;this.widgetBaseClass=namespace+'-'+name;this.options=$.extend({},$.widget.defaults,$[namespace][name].defaults,$.metadata&&$.metadata.get(element)[name],options);this.element=$(element).bind('setData.'+name,function(event,key,value){if(event.target==element){return self._setData(key,value);}}).bind('getData.'+name,function(event,key){if(event.target==element){return self._getData(key);}}).bind('remove',function(){return self.destroy();});};$[namespace][name].prototype=$.extend({},$.widget.prototype,prototype);$[namespace][name].getterSetter='option';};$.widget.prototype={_init:function(){},destroy:function(){this.element.removeData(this.widgetName).removeClass(this.widgetBaseClass+'-disabled'+' '+this.namespace+'-state-disabled').removeAttr('aria-disabled');},option:function(key,value){var options=key,self=this;if(typeof key=="string"){if(value===undefined){return this._getData(key);} options={};options[key]=value;} $.each(options,function(key,value){self._setData(key,value);});},_getData:function(key){return this.options[key];},_setData:function(key,value){this.options[key]=value;if(key=='disabled'){this.element [value?'addClass':'removeClass'](this.widgetBaseClass+'-disabled'+' '+ this.namespace+'-state-disabled').attr("aria-disabled",value);}},enable:function(){this._setData('disabled',false);},disable:function(){this._setData('disabled',true);},_trigger:function(type,event,data){var callback=this.options[type],eventName=(type==this.widgetEventPrefix?type:this.widgetEventPrefix+type);event=$.Event(event);event.type=eventName;if(event.originalEvent){for(var i=$.event.props.length,prop;i;){prop=$.event.props[--i];event[prop]=event.originalEvent[prop];}} this.element.trigger(event,data);return!($.isFunction(callback)&&callback.call(this.element[0],event,data)===false||event.isDefaultPrevented());}};$.widget.defaults={disabled:false};$.ui.mouse={_mouseInit:function(){var self=this;this.element.bind('mousedown.'+this.widgetName,function(event){return self._mouseDown(event);}).bind('click.'+this.widgetName,function(event){if(self._preventClickEvent){self._preventClickEvent=false;event.stopImmediatePropagation();return false;}});if($.browser.msie){this._mouseUnselectable=this.element.attr('unselectable');this.element.attr('unselectable','on');} this.started=false;},_mouseDestroy:function(){this.element.unbind('.'+this.widgetName);($.browser.msie&&this.element.attr('unselectable',this._mouseUnselectable));},_mouseDown:function(event){event.originalEvent=event.originalEvent||{};if(event.originalEvent.mouseHandled){return;} (this._mouseStarted&&this._mouseUp(event));this._mouseDownEvent=event;var self=this,btnIsLeft=(event.which==1),elIsCancel=(typeof this.options.cancel=="string"?$(event.target).parents().add(event.target).filter(this.options.cancel).length:false);if(!btnIsLeft||elIsCancel||!this._mouseCapture(event)){return true;} this.mouseDelayMet=!this.options.delay;if(!this.mouseDelayMet){this._mouseDelayTimer=setTimeout(function(){self.mouseDelayMet=true;},this.options.delay);} if(this._mouseDistanceMet(event)&&this._mouseDelayMet(event)){this._mouseStarted=(this._mouseStart(event)!==false);if(!this._mouseStarted){event.preventDefault();return true;}} this._mouseMoveDelegate=function(event){return self._mouseMove(event);};this._mouseUpDelegate=function(event){return self._mouseUp(event);};$(document).bind('mousemove.'+this.widgetName,this._mouseMoveDelegate).bind('mouseup.'+this.widgetName,this._mouseUpDelegate);($.browser.safari||event.preventDefault());event.originalEvent.mouseHandled=true;return true;},_mouseMove:function(event){if($.browser.msie&&!event.button){return this._mouseUp(event);} if(this._mouseStarted){this._mouseDrag(event);return event.preventDefault();} if(this._mouseDistanceMet(event)&&this._mouseDelayMet(event)){this._mouseStarted=(this._mouseStart(this._mouseDownEvent,event)!==false);(this._mouseStarted?this._mouseDrag(event):this._mouseUp(event));} return!this._mouseStarted;},_mouseUp:function(event){$(document).unbind('mousemove.'+this.widgetName,this._mouseMoveDelegate).unbind('mouseup.'+this.widgetName,this._mouseUpDelegate);if(this._mouseStarted){this._mouseStarted=false;this._preventClickEvent=(event.target==this._mouseDownEvent.target);this._mouseStop(event);} return false;},_mouseDistanceMet:function(event){return(Math.max(Math.abs(this._mouseDownEvent.pageX-event.pageX),Math.abs(this._mouseDownEvent.pageY-event.pageY))>=this.options.distance);},_mouseDelayMet:function(event){return this.mouseDelayMet;},_mouseStart:function(event){},_mouseDrag:function(event){},_mouseStop:function(event){},_mouseCapture:function(event){return true;}};$.ui.mouse.defaults={cancel:null,distance:1,delay:0};})(jQuery);
         (function($) {
            $.widget("ui.draggable", $.extend( {
               }
            , $.ui.mouse, {
               _init : function() {
                  if(this.options.helper == 'original' &&!(/^(?:r|a|f)/).test(this.element.css("position"))) this.element[0].style.position='relative';(this.options.addClasses&&this.element.addClass("ui-draggable"));(this.options.disabled&&this.element.addClass("ui-draggable-disabled"));this._mouseInit();},destroy:function(){if(!this.element.data('draggable'))return;this.element.removeData("draggable").unbind(".draggable").removeClass("ui-draggable" +" ui-draggable-dragging" +" ui-draggable-disabled");this._mouseDestroy();},_mouseCapture:function(event){var o=this.options;if(this.helper||o.disabled||$(event.target).is('.ui-resizable-handle')) return false;this.handle=this._getHandle(event);if(!this.handle) return false;return true;},_mouseStart:function(event){var o=this.options;this.helper=this._createHelper(event);this._cacheHelperProportions();if($.ui.ddmanager) $.ui.ddmanager.current=this;this._cacheMargins();this.cssPosition=this.helper.css("position");this.scrollParent=this.helper.scrollParent();this.offset=this.element.offset();this.offset={top:this.offset.top-this.margins.top,left:this.offset.left-this.margins.left};$.extend(this.offset,{click:{left:event.pageX-this.offset.left,top:event.pageY-this.offset.top},parent:this._getParentOffset(),relative:this._getRelativeOffset()});this.originalPosition=this._generatePosition(event);this.originalPageX=event.pageX;this.originalPageY=event.pageY;if(o.cursorAt) this._adjustOffsetFromHelper(o.cursorAt);if(o.containment) this._setContainment();this._trigger("start",event);this._cacheHelperProportions();if($.ui.ddmanager&&!o.dropBehaviour) $.ui.ddmanager.prepareOffsets(this,event);this.helper.addClass("ui-draggable-dragging");this._mouseDrag(event,true);return true;},_mouseDrag:function(event,noPropagation){this.position=this._generatePosition(event);this.positionAbs=this._convertPositionTo("absolute");if(!noPropagation){var ui=this._uiHash();this._trigger('drag',event,ui);this.position=ui.position;} if(!this.options.axis||this.options.axis!="y")this.helper[0].style.left=this.position.left+'px';if(!this.options.axis||this.options.axis!="x")this.helper[0].style.top=this.position.top+'px';if($.ui.ddmanager)$.ui.ddmanager.drag(this,event);return false;},_mouseStop:function(event){var dropped=false;if($.ui.ddmanager&&!this.options.dropBehaviour) dropped=$.ui.ddmanager.drop(this,event);if(this.dropped){dropped=this.dropped;this.dropped=false;} if((this.options.revert=="invalid"&&!dropped)||(this.options.revert=="valid"&&dropped)||this.options.revert===true||($.isFunction(this.options.revert)&&this.options.revert.call(this.element,dropped))){var self=this;$(this.helper).animate(this.originalPosition,parseInt(this.options.revertDuration,10),function(){self._trigger("stop",event);self._clear();});}else{this._trigger("stop",event);this._clear();} return false;},_getHandle:function(event){var handle=!this.options.handle||!$(this.options.handle,this.element).length?true:false;$(this.options.handle,this.element).find("*").andSelf().each(function(){if(this==event.target)handle=true;});return handle;},_createHelper:function(event){var o=this.options;var helper=$.isFunction(o.helper)?$(o.helper.apply(this.element[0],[event])):(o.helper=='clone'?this.element.clone():this.element);if(!helper.parents('body').length) helper.appendTo((o.appendTo=='parent'?this.element[0].parentNode:o.appendTo));if(helper[0]!=this.element[0]&&!(/(fixed|absolute)/).test(helper.css("position"))) helper.css("position","absolute");return helper;},_adjustOffsetFromHelper:function(obj){if(obj.left!=undefined)this.offset.click.left=obj.left+this.margins.left;if(obj.right!=undefined)this.offset.click.left=this.helperProportions.width-obj.right+this.margins.left;if(obj.top!=undefined)this.offset.click.top=obj.top+this.margins.top;if(obj.bottom!=undefined)this.offset.click.top=this.helperProportions.height-obj.bottom+this.margins.top;},_getParentOffset:function(){this.offsetParent=this.helper.offsetParent();var po=this.offsetParent.offset();if(this.cssPosition=='absolute'&&this.scrollParent[0]!=document&&$.ui.contains(this.scrollParent[0],this.offsetParent[0])){po.left+=this.scrollParent.scrollLeft();po.top+=this.scrollParent.scrollTop();} if((this.offsetParent[0]==document.body)||(this.offsetParent[0].tagName&&this.offsetParent[0].tagName.toLowerCase()=='html'&&$.browser.msie)) po={top:0,left:0};return{top:po.top+(parseInt(this.offsetParent.css("borderTopWidth"),10)||0),left:po.left+(parseInt(this.offsetParent.css("borderLeftWidth"),10)||0)};},_getRelativeOffset:function(){if(this.cssPosition=="relative"){var p=this.element.position();return{top:p.top-(parseInt(this.helper.css("top"),10)||0)+this.scrollParent.scrollTop(),left:p.left-(parseInt(this.helper.css("left"),10)||0)+this.scrollParent.scrollLeft()};}else{return{top:0,left:0};}},_cacheMargins:function(){this.margins={left:(parseInt(this.element.css("marginLeft"),10)||0),top:(parseInt(this.element.css("marginTop"),10)||0)};},_cacheHelperProportions:function(){this.helperProportions={width:this.helper.outerWidth(),height:this.helper.outerHeight()};},_setContainment:function(){var o=this.options;if(o.containment=='parent')o.containment=this.helper[0].parentNode;if(o.containment=='document'||o.containment=='window')this.containment=[0-this.offset.relative.left-this.offset.parent.left,0-this.offset.relative.top-this.offset.parent.top,$(o.containment=='document'?document:window).width()-this.helperProportions.width-this.margins.left,($(o.containment=='document'?document:window).height()||document.body.parentNode.scrollHeight)-this.helperProportions.height-this.margins.top];if(!(/^(document|window|parent)$/).test(o.containment)&&o.containment.constructor!=Array){var ce=$(o.containment)[0];if(!ce)return;var co=$(o.containment).offset();var over=($(ce).css("overflow")!='hidden');this.containment=[co.left+(parseInt($(ce).css("borderLeftWidth"),10)||0)+(parseInt($(ce).css("paddingLeft"),10)||0)-this.margins.left,co.top+(parseInt($(ce).css("borderTopWidth"),10)||0)+(parseInt($(ce).css("paddingTop"),10)||0)-this.margins.top,co.left+(over?Math.max(ce.scrollWidth,ce.offsetWidth):ce.offsetWidth)-(parseInt($(ce).css("borderLeftWidth"),10)||0)-(parseInt($(ce).css("paddingRight"),10)||0)-this.helperProportions.width-this.margins.left,co.top+(over?Math.max(ce.scrollHeight,ce.offsetHeight):ce.offsetHeight)-(parseInt($(ce).css("borderTopWidth"),10)||0)-(parseInt($(ce).css("paddingBottom"),10)||0)-this.helperProportions.height-this.margins.top];}else if(o.containment.constructor==Array){this.containment=o.containment;}},_convertPositionTo:function(d,pos){if(!pos)pos=this.position;var mod=d=="absolute"?1:-1;var o=this.options,scroll=this.cssPosition=='absolute'&&!(this.scrollParent[0]!=document&&$.ui.contains(this.scrollParent[0],this.offsetParent[0]))?this.offsetParent:this.scrollParent,scrollIsRootNode=(/(html|body)/i).test(scroll[0].tagName);return{top:(pos.top +this.offset.relative.top*mod +this.offset.parent.top*mod -($.browser.safari&&this.cssPosition=='fixed'?0:(this.cssPosition=='fixed'?-this.scrollParent.scrollTop():(scrollIsRootNode?0:scroll.scrollTop()))*mod)),left:(pos.left +this.offset.relative.left*mod +this.offset.parent.left*mod -($.browser.safari&&this.cssPosition=='fixed'?0:(this.cssPosition=='fixed'?-this.scrollParent.scrollLeft():scrollIsRootNode?0:scroll.scrollLeft())*mod))};},_generatePosition:function(event){var o=this.options,scroll=this.cssPosition=='absolute'&&!(this.scrollParent[0]!=document&&$.ui.contains(this.scrollParent[0],this.offsetParent[0]))?this.offsetParent:this.scrollParent,scrollIsRootNode=(/(html|body)/i).test(scroll[0].tagName);if(this.cssPosition=='relative'&&!(this.scrollParent[0]!=document&&this.scrollParent[0]!=this.offsetParent[0])){this.offset.relative=this._getRelativeOffset();} var pageX=event.pageX;var pageY=event.pageY;if(this.originalPosition){if(this.containment){if(event.pageX-this.offset.click.leftthis.containment[2])pageX=this.containment[2]+this.offset.click.left;if(event.pageY-this.offset.click.top>this.containment[3])pageY=this.containment[3]+this.offset.click.top;} if(o.grid){var top=this.originalPageY+Math.round((pageY-this.originalPageY)/o.grid[1])*o.grid[1];pageY=this.containment?(!(top-this.offset.click.topthis.containment[3])?top:(!(top-this.offset.click.topthis.containment[2])?left:(!(left-this.offset.click.left').css({width:this.offsetWidth+"px",height:this.offsetHeight+"px",position:"absolute",opacity:"0.001",zIndex:1000}).css($(this).offset()).appendTo("body");});},stop:function(event,ui){$("div.ui-draggable-iframeFix").each(function(){this.parentNode.removeChild(this);});}});$.ui.plugin.add("draggable","opacity",{start:function(event,ui){var t=$(ui.helper),o=$(this).data('draggable').options;if(t.css("opacity"))o._opacity=t.css("opacity");t.css('opacity',o.opacity);},stop:function(event,ui){var o=$(this).data('draggable').options;if(o._opacity)$(ui.helper).css('opacity',o._opacity);}});$.ui.plugin.add("draggable","scroll",{start:function(event,ui){var i=$(this).data("draggable");if(i.scrollParent[0]!=document&&i.scrollParent[0].tagName!='HTML')i.overflowOffset=i.scrollParent.offset();},drag:function(event,ui){var i=$(this).data("draggable"),o=i.options,scrolled=false;if(i.scrollParent[0]!=document&&i.scrollParent[0].tagName!='HTML'){if(!o.axis||o.axis!='x'){if((i.overflowOffset.top+i.scrollParent[0].offsetHeight)-event.pageY=0;i--){var l=inst.snapElements[i].left,r=l+inst.snapElements[i].width,t=inst.snapElements[i].top,b=t+inst.snapElements[i].height;if(!((l-d                  (function($) {
                     $.widget("ui.droppable", {
                        _init : function() {
                           var o = this.options, accept = o.accept; this.isover = 0; this.isout = 1; this.options.accept = this.options.accept && $.isFunction(this.options.accept) ? this.options.accept : function(d) {
                              return d.is(accept); }; this.proportions = {
                              width : this.element[0].offsetWidth, height : this.element[0].offsetHeight}; $.ui.ddmanager.droppables[this.options.scope] = $.ui.ddmanager.droppables[this.options.scope] || []; $.ui.ddmanager.droppables[this.options.scope].push(this); (this.options.addClasses && this.element.addClass("ui-droppable")); }
                        , destroy : function() {
                           var drop = $.ui.ddmanager.droppables[this.options.scope]; for(var i = 0; i < drop.length; i++)if(drop[i] == this)drop.splice(i, 1); this.element.removeClass("ui-droppable ui-droppable-disabled").removeData("droppable").unbind(".droppable"); }
                        , _setData : function(key, value) {
                           if(key == 'accept') {
                              this.options.accept = value && $.isFunction(value) ? value : function(d) {
                                 return d.is(accept); }; }
                           else {
                              $.widget.prototype._setData.apply(this, arguments); }
                           }
                        , _activate : function(event) {
                           var draggable = $.ui.ddmanager.current; if(this.options.activeClass)this.element.addClass(this.options.activeClass); (draggable && this._trigger('activate', event, this.ui(draggable))); }
                        , _deactivate : function(event) {
                           var draggable = $.ui.ddmanager.current; if(this.options.activeClass)this.element.removeClass(this.options.activeClass); (draggable && this._trigger('deactivate', event, this.ui(draggable))); }
                        , _over : function(event) {
                           var draggable = $.ui.ddmanager.current; if(!draggable || (draggable.currentItem || draggable.element)[0] == this.element[0])return; if(this.options.accept.call(this.element[0], (draggable.currentItem || draggable.element))) {
                              if(this.options.hoverClass)this.element.addClass(this.options.hoverClass); this._trigger('over', event, this.ui(draggable)); }
                           }
                        , _out : function(event) {
                           var draggable = $.ui.ddmanager.current; if(!draggable || (draggable.currentItem || draggable.element)[0] == this.element[0])return; if(this.options.accept.call(this.element[0], (draggable.currentItem || draggable.element))) {
                              if(this.options.hoverClass)this.element.removeClass(this.options.hoverClass); this._trigger('out', event, this.ui(draggable)); }
                           }
                        , _drop : function(event, custom) {
                           var draggable = custom || $.ui.ddmanager.current; if(!draggable || (draggable.currentItem || draggable.element)[0] == this.element[0])return false; var childrenIntersection = false; this.element.find(":data(droppable)").not(".ui-draggable-dragging").each(function() {
                              var inst = $.data(this, 'droppable'); if(inst.options.greedy && $.ui.intersect(draggable, $.extend(inst, {
                                 offset : inst.element.offset()}
                              ), inst.options.tolerance)) {
                                 childrenIntersection = true; return false; }
                              }
                           ); if(childrenIntersection)return false; if(this.options.accept.call(this.element[0], (draggable.currentItem || draggable.element))) {
                              if(this.options.activeClass)this.element.removeClass(this.options.activeClass); if(this.options.hoverClass)this.element.removeClass(this.options.hoverClass); this._trigger('drop', event, this.ui(draggable)); return this.element; }
                           return false; }
                        , ui : function(c) {
                           return {
                              draggable : (c.currentItem || c.element), helper : c.helper, position : c.position, absolutePosition : c.positionAbs, offset : c.positionAbs}; }
                        }
                     ); $.extend($.ui.droppable, {
                        version : "1.7", eventPrefix : 'drop', defaults : {
                           accept : '*', activeClass : false, addClasses : true, greedy : false, hoverClass : false, scope : 'default', tolerance : 'intersect'}
                        }
                     ); $.ui.intersect = function(draggable, droppable, toleranceMode) {
                        if(!droppable.offset)return false; var x1 = (draggable.positionAbs || draggable.position.absolute).left, x2 = x1 + draggable.helperProportions.width, y1 = (draggable.positionAbs || draggable.position.absolute).top, y2 = y1 + draggable.helperProportions.height; var l = droppable.offset.left, r = l + droppable.proportions.width, t = droppable.offset.top, b = t + droppable.proportions.height; switch(toleranceMode) {
                           case'fit':return(l < x1 && x2 < r && t < y1 && y2 < b); break; case'intersect':return(l < x1 + (draggable.helperProportions.width / 2) && x2 - (draggable.helperProportions.width / 2) < r && t < y1 + (draggable.helperProportions.height / 2) && y2 - (draggable.helperProportions.height / 2) < b); break; case'pointer':var draggableLeft = ((draggable.positionAbs || draggable.position.absolute).left + (draggable.clickOffset || draggable.offset.click).left), draggableTop = ((draggable.positionAbs || draggable.position.absolute).top + (draggable.clickOffset || draggable.offset.click).top), isOver = $.ui.isOver(draggableTop, draggableLeft, t, l, droppable.proportions.height, droppable.proportions.width); return isOver; break; case'touch':return((y1 >= t && y1 <= b) || (y2 >= t && y2 <= b) || (y1 < t && y2 > b)) && ((x1 >= l && x1 <= r) || (x2 >= l && x2 <= r) || (x1 < l && x2 > r)); break; default : return false; break; }
                        }; $.ui.ddmanager = {
                        current : null, droppables : {
                           'default':[]}
                        , prepareOffsets : function(t, event) {
                           var m = $.ui.ddmanager.droppables[t.options.scope]; var type = event ? event.type : null; var list = (t.currentItem || t.element).find(":data(droppable)").andSelf(); droppablesLoop : for(var i = 0; i < m.length; i++) {
                              if(m[i].options.disabled || (t &&!m[i].options.accept.call(m[i].element[0], (t.currentItem || t.element))))continue; for(var j = 0; j < list.length; j++) {
                                 if(list[j] == m[i].element[0]) {
                                    m[i].proportions.height = 0; continue droppablesLoop; }
                                 }; m[i].visible = m[i].element.css("display") != "none"; if(!m[i].visible)continue; m[i].offset = m[i].element.offset(); m[i].proportions = {
                                 width : m[i].element[0].offsetWidth, height : m[i].element[0].offsetHeight}; if(type == "mousedown")m[i]._activate.call(m[i], event); }
                           }
                        , drop : function(draggable, event) {
                           var dropped = false; $.each($.ui.ddmanager.droppables[draggable.options.scope], function() {
                              if(!this.options)return; if(!this.options.disabled && this.visible && $.ui.intersect(draggable, this, this.options.tolerance))dropped = this._drop.call(this, event); if(!this.options.disabled && this.visible && this.options.accept.call(this.element[0], (draggable.currentItem || draggable.element))) {
                                 this.isout = 1; this.isover = 0; this._deactivate.call(this, event); }
                              }
                           ); return dropped; }
                        , drag : function(draggable, event) {
                           if(draggable.options.refreshPositions)$.ui.ddmanager.prepareOffsets(draggable, event); $.each($.ui.ddmanager.droppables[draggable.options.scope], function() {
                              if(this.options.disabled || this.greedyChild ||!this.visible)return; var intersects = $.ui.intersect(draggable, this, this.options.tolerance); var c =!intersects && this.isover == 1 ? 'isout':(intersects && this.isover == 0 ? 'isover':null); if(!c)return; var parentInstance; if(this.options.greedy) {
                                 var parent = this.element.parents(':data(droppable):eq(0)'); if(parent.length) {
                                    parentInstance = $.data(parent[0], 'droppable'); parentInstance.greedyChild = (c == 'isover' ? 1 : 0); }
                                 }
                              if(parentInstance && c == 'isover') {
                                 parentInstance['isover'] = 0; parentInstance['isout'] = 1; parentInstance._out.call(parentInstance, event); }
                              this[c] = 1; this[c == 'isout' ? 'isover':'isout'] = 0; this[c == "isover" ? "_over" : "_out"].call(this, event); if(parentInstance && c == 'isout') {
                                 parentInstance['isout'] = 0; parentInstance['isover'] = 1; parentInstance._over.call(parentInstance, event); }
                              }
                           ); }
                        }; }
                  )(jQuery);
                  client = new Object();
                  client.markers = new Object();
                  client.data = new Object();
                  client.pageplugins = new Object();
                  client.pageplugins.displayplugins = new Object();
                  client.widgets = new Object();
                  client.integration = new Object();
                  config = new Object();
                  config.imagePath = null;
                  config.SHIFT_RESEARCH_LEFT = 160;
                  config.SHIFT_RESEARCH_RIGHT = 260;
                  config.SHIFT_RESEARCH_RIGHT_WIDE = config.SHIFT_RESEARCH_RIGHT;
                  config.RESEARCH_PANEL_WIDTH = 500;
                  config.CARD_LIMITS = [21, 4, 0, 4];
                  client.lang = {
                     code : 'en', _H1 : 'Heading 1', _H2 : 'Heading 2', _H3 : 'Heading 3', ADD_LEFT : 'Insert note sheet left', ADD_REFERENCE : 'Add reference', ADD_RIGHT : 'Insert note sheet right', ADD_TRANSLATION : 'Add', ANNOTATION : 'Annotation', ANNOTATE : 'Annotate', ANSWER : 'Answer', CORRECT_TRANSLATION : 'Correct translation', ANSWER_MODE_BY_RANGE : 'Use selection', ANSWER_MODE_EXPLICIT : 'Define explicitly', ANSWER_MODE : 'Answer mode', ANSWER_RIGHT : 'Knew it', ANSWER_TOO_LONG : 'Answer is too long.', ANSWER_UNSURE : 'Repeat again later', ANSWER_WRONG : 'I was wrong', BOLD : 'Bold', BOX_LEVEL_BRIDGE : ' on box level ', CANCEL : 'Cancel', CAPTCHA : 'Security code', CAPTCHA_MISSING : 'Security code missing', CARD_EMPTY : '(This note sheet is empty)', NOTE_SHEET : 'Note sheet', CATEGORY : 'Keywords', CLICK_TO_ENTER : '(Click here to begin taking your notes)', CLICK_TO_ENTER_ADVANCED : '(Click here to take advanced notes)', CLICK_TO_ENTER_ILLUSTRATIVE : '(Click here to illustrate the notes you\'ve taken so far)', ILLUSTRATION_OF : 'Illustration of ', MORE_DETAILS_ON : 'More details on ', CLOSE : 'Close', CONFIRM_CLOSE : 'If you confirm, all unsaved changes will be lost.', CONFIRM_REQUEST_SENT : 'A confirmation email has been sent to your email address.', ACCOUNT_CREATED : 'Account was created, use your credentials to log in.', CONNECTION_DOWN : 'The connection could not be kept alive. Try reloading the page.', CONNECTION_ERROR : 'The connection was lost. Try reloading the page.', COPY : 'Copy selection', COPY_SECURITY : 'The copy command is not available due to restrictions set by your browser. Please use the shortcut Ctrl+C instead.', COULD_NOT_UPDATE_TOPIC : 'Could not update note folder', COULDNT_COPY_IMAGE : 'Could not copy the image to the destination folder', CREATE : 'Create', CUT : 'Cut selection', CUT_SECURITY : 'The cut command is not available due to restrictions set by your browser. Please use the shortcut Ctrl+X instead.', DEFINITION : 'Definition', DEFINE_SELECTION : 'Define', DEFINITION_QUESTION : 'What is the definition of:', DELETE : 'Delete', DESCRIBE_ISSUE : '(Click here to describe the issue)', DESCRIPTION : 'Describe the new feature', MEDIABIRD_DESCRIPTION : '<p>Mediabird is about learning together. Users can take notes on subjects they want to learn and discuss questions in the context they appear.</p><p>Enter your credentials and click "Log in" or register for a new account using "Register new account".</p>', BROWSER_BAD_TEXT : '<h3>Your browser or browser version is not supported. Update your browser or install the recommended browser <a href="http://www.getfirefox.com" target="_blank">Firefox</a></h3>', EDIT_CARD_FROM_MAP : 'Edit content of this note sheet', EDIT_EMPTY_CARD_FROM_MAP : 'Create content for this note sheet', SAVE : 'Save', SAVE_CLOSE : 'Save and close', CANNOT_DELETE_WHILE_OPEN : 'Cannot delete the currently opened note sheet. Open a different note sheet and try again.', CANNOT_DELETE_LAST : 'Cannot delete the last opened note sheet. Click on "All note folders..." and delete the note folder.', TRAINER_HEADING : 'Memorize note folder', EDIT : 'Edit', EDIT_MAP : '<h1>Rearrange the note sheets with the mouse.</h1><p>Drag them <strong>up and down</strong> to modify their level.</p>', EXTEND_MAP : '<h1>Create new note sheets or navigate to existing ones.</h1><p>Drag new items <strong>up and down</strong> to modify their level.</p>', EDIT_MAP_ADD : '<h1>You\'ve reached the last note sheet.</h1><p>You can <strong>add more</strong> using the \'+\'-button.</p>', EDIT_MAP_ADD_LEFT : '<h1>You are at the first note sheet.</h1><p>You can <strong>add more</strong> using the \'+\'-button.</p>', SHOW_MAP : '<h1>Overview of the note folder.</h1>', EDITOR_TITLE : 'Editor', EMAIL : 'Email', EMAIL_MISSING : 'Email missing', INVALID_EMAIL : 'You have entered an invalid email address', EMPTY_ROW : '(There are no items in the list)', ENTER_ANNOTATION : '(Click here to enter annotation)', ENTER_ANSWER : '(Click here to enter answer)', ENTER_DEFINITION : '(Click here to enter the definition of the selected expression)', SUGGEST_ANSWER : '(Click here if you want to suggest an answer to the question)', ENTER_EXPRESSION : '(Click here and enter the expression defined by the selection)', ENTER_NEW_CARD_TITLE : 'Enter a new title for the note sheet', ENTER_QUESTION : '(Click here to enter question)', ERROR_CHANGING_STATUS : 'Could not update the access rights for this group. Try updating it again.', ERROR_CREATING_USER : 'There was an error during the creation of the new user.', ERROR_FUNCTION_DISABLED : 'The function was disabled by the administrator.', ERROR_LOADING_TRAINING : 'Last loading operation not successful', ERROR_MOVE : 'Error while uploading file', ERROR_TYPE : 'The file type is invalid. Please only upload images.', ERROR_NO_FILE : 'No file was uploaded.', ERROR_QUOTA : 'You have reached the quota limit of your account.', ERROR_SENDING_CONFIRM : 'There was an error sending the confirmation email.', ERROR_TOO_BIG : 'The image you have selected is too big.', ERROR_WHILE_UPLOADING : 'The image could not be uploaded.', FILE : 'File', FLASH_CARD_BOXES : 'Card boxes', FIND_HOW_TO : 'Use CTRL+F to search the current note sheet.', FIND : 'Find...', FLOAT_LEFT : 'Wrap text on the right', FLOAT_NONE : 'No float', FLOAT_RIGHT : 'Wrap text on the left', FLOAT_STYLE : 'Float style', FORMAT : 'Format', FORMULA_CONTAINS_BLACKLISTED : 'LaTeX code contains blacklisted tags.', FORMULA_TOO_BIG : 'Formular too big to be displayed', FORMULA_TOO_LONG : 'Equation too long', GENERAL_PREFIX : 'Think of the answer to the following flash card from note sheet ', GET_NEW_CAPTCHA : 'Get a new code', GET_PASSWORD : 'Forgot password', GHOST_ENTRY : '(click here to add a new item)', GHOST_TOPIC : '(click here to add a new note folder)', GO : 'Go', GOTO_ANSWER : 'Show answer', GOTO_QUESTION : 'Show question again', SHOW_CONTEXT : 'Show context', CONTEXT : 'Context', GROUP : 'Group', HEIGHT : 'Height', HIGHLIGHT : 'Highlight', HTML_CODE : 'HTML Code', CODE : 'Code', IMP_LEVEL : 'Marked as important', IMP_LEVEL_BY : 'Marked as important by', MARK_IMPORTANT : 'Mark important', IN : 'in', INDENT : 'Increase depth', INSERT_HTML : 'HTML', INSERT_CODE : 'Sample code', INSERT_IMAGE : 'Image', INSERT : 'Insert', INSERT_LATEX : 'Equation', AUTO_CONVERT_LATEX : 'Automatic', AUTO_CONVERT_EXPLANATION : 'Automatically converts LaTeX code from the current note sheet into equations', AUTO_LATEX_NO_JOBS : 'No LaTeX code was found on the note sheet', LATEX_SOURCE : 'Equation', INSERT_LINK : 'Link', INSERTORDEREDLIST : 'Enumeration', INSERTUNORDEREDLIST : 'Bullet points', INVALID_END : '" is invalid', INVALID_START : 'The value of "', IS_IN : 'is written in', ISSUE : 'Problem', ASK_QUESTION : 'Ask question', ANSWER_LINK : 'Insert question', ITALIC : 'Italic', INVITE : 'Invite', NO_INVITEES_SELECTED : 'You did not specify who you want to invite to the group', INVITE_USER : 'Invite a friend', INVITE_USERS_TO_GROUP : 'Invite friend to group', FOLLOWING_NOT_FOUND : 'The following friends could not be invited since they are not registered in Mediabird: ', NONE_INVITED : 'Your friends have already been invited.', COULD_NOT_INVITE_USERS : 'Could not invite friends', EMAIL_OR_NAME : 'Email addresses', NO_USERS_TO_INVITE : '(No known friends can be invited)', FURTHER_USERS_INVITE : 'By email address', EMAIL_SEPARATE_EXPLANATION : 'Separate email addresses with commas.', KNOWN_USERS : 'Friends', JUST_ENABLED : 'Your account has been successfully activated. Use the form below to log in.', JUSTIFY_CENTER : 'Align center', JUSTIFY_FULL : 'Align justify', JUSTIFY_LEFT : 'Align left', JUSTIFY_RIGHT : 'Align right', LANGUAGES : ['English', 'Afar', 'Abkhazian', 'Afrikaans', 'Akan', 'Albanian', 'Amharic', 'Arabic', 'Aragonese', 'Armenian', 'Assamese', 'Avaric', 'Avestan', 'Aymara', 'Azerbaijani', 'Bambara', 'Bashkir', 'Basque', 'Belarusian', 'Bengali', 'Bihari', 'Bislama', 'Bosnian', 'Breton', 'Bulgarian', 'Burmese', 'Catalan', 'Chamorro', 'Chechen', 'Chichewa', 'Chinese', 'Church Slavic', 'Chuvash', 'Cornish', 'Corsican', 'Cree', 'Croatian', 'Czech', 'Danish', 'Divehi', 'Dutch', 'Dzongkha', 'Esperanto', 'Estonian', 'Ewe', 'Faroese', 'Fijian', 'Finnish', 'French', 'Fulah', 'Galician', 'Ganda', 'Georgian', 'German', 'Greek', 'Guarani', 'Gujarati', 'Haitian', 'Hausa', 'Hebrew', 'Herero', 'Hindi', 'Hiri Motu', 'Hungarian', 'Icelandic', 'Ido', 'Igbo', 'Indonesian', 'Interlingue', 'Inuktitut', 'Inupiaq', 'Irish', 'Italian', 'Japanese', 'Javanese', 'Kalaallisut', 'Kannada', 'Kanuri', 'Kashmiri', 'Kazakh', 'Khmer', 'Kikuyu', 'Kinyarwanda', 'Kirghiz', 'Kirundi', 'Komi', 'Kongo', 'Korean', 'Kurdish', 'Kwanyama', 'Lao', 'Latin', 'Latvian', 'Limburgish', 'Lingala', 'Lithuanian', 'Luba-Katanga', 'Luxembourgish', 'Macedonian', 'Malagasy', 'Malay', 'Malayalam', 'Maltese', 'Manx', 'Maori', 'Marathi', 'Marshallese', 'Moldavian', 'Mongolian', 'Nauru', 'Navajo', 'Ndonga', 'Nepali', 'North Ndebele', 'Northern Sami', 'Norwegian', 'Norwegian Bokmal', 'Norwegian Nynorsk', 'Occitan', 'Ojibwa', 'Oriya', 'Oromo', 'Ossetian', 'Pāli', 'Panjabi', 'Pashto', 'Persian', 'Polish', 'Portuguese', 'Quechua', 'Raeto-Romance', 'Romanian', 'Russian', 'Samoan', 'Sango', 'Sanskrit', 'Sardinian', 'Scottish Gaelic', 'Serbian', 'Serbo-Croatian', 'Shona', 'Sichuan Yi', 'Sindhi', 'Sinhala', 'Slovak', 'Slovenian', 'Somali', 'Sotho', 'South Ndebele', 'Spanish', 'Sundanese', 'Swahili', 'Swati', 'Swedish', 'Tagalog', 'Tahitian', 'Tajik', 'Tamil', 'Tatar', 'Telugu', 'Thai', 'Tibetan', 'Tigrinya', 'Tonga', 'Tsonga', 'Tswana', 'Turkish', 'Turkmen', 'Twi', 'Uighur', 'Ukrainian', 'Urdu', 'Uzbek', 'Venda', 'Vietnamese', 'Volapuk', 'Walloon', 'Welsh', 'Western Frisian', 'Wolof', 'Xhosa', 'Yiddish', 'Yoruba', 'Zhuang', 'Zulu', 'Latin', 'Old Greek', 'Fusha'], LEVEL : 'Level', LEVEL_IMPORTANT : 'Important', LEVEL_ADVANCED : 'Advanced', LEVEL_ILLUSTRATIVE : 'Illustrative', LINK_HEADING : 'Link editor', LINK_TYPE : 'Link type', LINK_REQUIRES_SELECTION : 'Inserting a link requires a selection which is to be linked', COLLAPSIBLE_REQUIRES_SELECTION : 'Inserting a collapsible note requires a selection which is to be collapsible', LOCATION : 'Location', LOGON : 'Log in', COULD_NOT_CLOSE_SESSION : 'Could not close the session. Reload the page to start a new session.', SET_ACCESS_NONE : 'No access', SET_ACCESS_READ : 'Read-only access', SET_ACCESS_WRITE : 'Write access', SET_ACCESS_STRUCTURE : 'Full access', TOO_MANY_STEPS : 'You have reached the maximum number of note sheets in this note folder.', MOVE_LEFT : 'Move left', MOVE_RIGHT : 'Move right', NAME : 'Name', NAME_MISSING : 'Friends name missing', NO_ANSWER : 'You didn\'t enter an answer.', NO_ANNOTATION : 'You didn\'t enter an annotation.', NO_DEFINITION : 'You didn\'t enter a definition.', NO_QUESTION : 'You didn\'t enter a question.', NO_EXPRESSION : 'You didn\'t entered an expression.', NO_CARD_TITLE : '(none selected)', NO_FLASH_CARDS : 'The current sheet does not contain any flash cards.', NO_HIGHLIGHT_IN_EMPTY_CARD : 'The highlight function is not available for empty note sheets', NO_URL_GIVEN : 'To insert an image from a URL, you have to specify a location.', NO_MARKER_FOR_DEL : 'Select tag which you want to delete first', NOCAT : '(No keywords)', NOT_CONFIRMED : 'Please confirm your email address using the link in the confirmation email sent to you.', OK : 'OK', OTHER_ERROR : 'Error while accessing script.', OUTDENT : 'Decrease depth', PADDING_BOTTOM : 'Bottom', PADDING_LEFT : 'Left', PADDING_RIGHT : 'Right', PADDING_STYLE : 'Padding style', PADDING_TOP : 'Top', PARA : 'Paragraph', PASS_MISSING : 'Password missing', PASS_NOT_MATCHING : 'Passwords mismatch', PASSWORD_ACCESS_KEY : 'P', PASSWORD_ERROR : 'Error occurred while retrieving your password.', PASSWORD_INVALID : 'There was an error sending your password to the specified email address.', PASSWORD : 'Password', PASSWORD_AGAIN : 'Repeat password', PASSWORD_NO_USER : 'The email address you have entered does not correspond to an existing account.', PASSWORD_SENT : 'An email with your password has been sent to the email address associated with the account.', PASTE : 'Paste into selection', PASTE_SECURITY : 'The paste command is not available due to restrictions set by your browser. Please use the shortcut Ctrl+V instead.', PLEASE_TRANSLATE : 'Translate into ', QUESTION_EDIT : 'Click to edit ', QUESTION : 'Question', QUESTION_MODE : 'Tag type', QUESTION_TOO_LONG : 'Question is too long.', QUESTION_VIEW : 'Click to view ', REFERENCE : 'Reference', REDO : 'Redo', REMOVE_FORMAT : 'Clear formatting', REMOVE_LINK : 'Remove link', REMOVE : 'Remove', REMOVE_THIS : 'Delete tag', REMOVE_TRANSLATION : 'Remove', RENAME : 'Rename', RENDERING_FAILED : 'Rendering of equation did not succeed.', REP_LEVEL : 'Marked for repetition', REP_MARKER : 'Repeat later', SHARE : 'Share', REPEAT : 'Repeat', REPEAT_TITLE : 'The highlighted region was marked for repetition.', REVIEW_RIGHT : 'Knew it', REVIEW_UNSURE : 'Decide later', REVIEW_WRONG : 'Repeat again', SEARCH : 'Search', SEL_REQUIRED_HIGHLIGHT : 'Before highlighting, select the piece of text you want to highlight.', SELECT_CARD_EXPLANATION : 'Click on a note sheet and click on "Select note sheet".', SELECT_CARD : 'Select note sheet ', SELECTED_TEXT : 'Selection', SET : 'Set', SESSION_CREATION_ERROR : 'The was an error creation the session.', SESSION_FINISHED : 'Training finished! There are no flash cards left in this session.', SESSION_ABORTED : 'The page loading was aborted', SESSION_INVALID : 'An invalid reply was sent. This might be a bug or a session timeout. Try reloading the page.', SIGN_UP : 'Register new account', STRIKE_THROUGH : 'Strike through', STUDY_CARD_FROM_MAP : 'Show this note sheet', STUDY_TOOL_TITLE : 'Viewer', TERMS_ACCEPT : 'I have read and understood the <a href="terms.php" target="_blank">Terms of Use</a>.', TERMS : 'Terms of Use', TERMS_MISSING : 'You have to accept the Terms of Use', TITLE : 'Title', MEDIABIRD_TITLE : 'Mediabird Web2.0-Learning', TOPIC_BRIDGE : ', note folder ', TOPIC : 'Note folder', TOPIC_EMPTY : 'The note folder does not contain any note sheets and cannot be opened.', TOPICS : 'Note folder list', TRANSLATE_INTO : 'Translate into', TRANSLATION_EDITOR : 'Translation Editor', TRANSLATION_INTO : 'In', TRANSLATION : 'Translation', TRANSLATE : 'Translate', TRANSLATION_MARKER : 'Edit translation', TRANSLATION_VIEWER : 'View translation', TYPE_CARD : 'Note sheet', TYPE_EMAIL : 'Email', TYPE_URL : 'URL', UNDERLINE : 'Underline', UNDO : 'Undo', UNTITLED : 'Untitled', UPDATE : 'Update', UPLOAD_BUTTON : 'Upload', PICTURE : 'Picture', URL_INVALID : 'The URL you have specified is invalid', USER_EMAIL_TAKEN : 'The email address you have entered is already in use by another account.', USER_NAME_TAKEN : 'The user name you have chosen is already in use by another account.', WIDTH : 'Width', WRONG_PASSWORD : 'The combination of user name and password is wrong.', WRONG_SECURITY : 'Security code entered incorrectly', INSERT_COLLAPSIBLE_NOTE_TEXT : '(Enter text)', INSERT_COLLAPSIBLE : 'Collapsible', ADD_PREREQUISITE : 'Add prerequisite', REMOVE_PREREQUISITE : 'Remove prerequisite', PREREQUISITES : 'Prerequisites', NO_TITLE_GIVEN : 'You didn\'t enter a title for the prerequisite', NO_TOPIC_SELECTED : 'You didn\'t select a note folder as a prerequisite', PREREQUISITE : 'Prerequisite for this note folder', EDIT_FIRST_CARD_OF_PREREQUISITE : 'Edit first note sheet of prerequisite', STUDY_PREREQUISITE : 'Study prerequisite', IMAGE_ADJUST_HEADING : 'Adjust image properties', IMAGE_UPLOAD_HEADING : 'Upload new picture', IMAGE_LOCATION_HEADING : 'Use given location', COULD_NOT_LOAD_TOPICS : 'Could not retrieve the note folder list', COULD_NOT_LOAD_GROUPS : 'Could not retrieve the group list', CREATE_GROUP : 'Create group', VIEW_MY_GROUPS : 'View my groups', REQUEST_LIST_HEADER : 'Group invitations and membership requests', JOIN_GROUP : 'Join group', REQUEST_GROUP : 'Request membership', LEAVE_GROUP : 'Leave group', ACCEPT_MEMBER : 'Accept', REJECT_MEMBER : 'Deny', PENDING : 'Pending', INVITED : 'Invited', REQUESTED : 'Requested', MEMBER_LIST_HEADERS : ['Name', 'Level'], REQUESTS_LIST_HEADERS : ['Group', 'Info', 'Actions'], SET_TO : 'Set to', ADMIN_LEVEL : 'Admin', MEMBER_LEVEL : 'Member', EDIT_GROUP_HEADER : 'Edit group', VIEW_GROUP_HEADER : 'View group', GROUP_DESCRIPTION : 'Description', GROUP_NAME : 'Name', GROUP_CATEGORY : 'Keywords', GROUP_ACCESS : 'Non-member access', GROUP_NO_ACCESS : 'Hide', GROUP_VIEW_ACCESS : 'Show, allow requesting', GROUP_JOIN_ACCESS : 'Show, allow joining', COULD_NOT_UPDATE_GROUP : 'Could not update the group', MEMBER_LIST : 'Members', COULD_NOT_SET_MEMBER_ACCESS : 'Could not change the member\'s access level', COULD_NOT_LEAVE_GROUP : 'Could not leave group', COULD_NOT_JOIN_GROUP : 'Could not join group', COULD_NOT_DELETE_GROUP : 'Could not delete group', CANNOT_REMOVE_NONEMPTY_GROUP : 'Cannot delete group which still has members. Remove the members first.', ACCEPT_INVITATION : 'Accept', REJECT_INVITATION : 'Ignore', CANCEL_REQUEST : 'Remove request', COULD_NOT_REMOVE_REQUEST : 'Could not remove request', COULD_NOT_ACCEPT_REQUEST : 'Could not accept membership request', COULD_NOT_REJECT_REQUEST : 'Could not reject membership request', COULD_NOT_REMOVE_MEMBER : 'Could not remove membership', CANNOT_LEAVE_GROUP_FROM_EDITOR : 'Use the group manager to leave groups you have joined.', REMOVE_MEMBER : 'Remove from group', TOPIC_LIST_RECENT_HEADING : 'History', GROUPLIST_HEADINGS : ['Group', 'Access'], GROUPLIST_HEADER : 'Shares', CLICK_TO_CHANGE_ACCESS : 'Click to change the group members\' access level', ADD_GROUP : 'Share note folder', SHARE_FOLDER : 'Manage sharing', SHARE_FOLDER_EXPLANATION : 'Manage shares of current note folder', SHARE_TOPIC_WITH_GROUP : 'New share for current note folder', FILE_AS : "Publish as", MEMBER_RIGHTS : 'Member rights', RIGHT_READONLY_PRESET : 'View', RIGHT_WRITE_PRESET : 'View and edit', RIGHT_STUCTURE_PRESET : 'View, edit and re-arrange', NOT_MEMBER_OF_ANY_GROUP : 'You cannot share a note folder while you are not member of a group. Click on "Create group" to create a group.', COULD_NOT_UPDATE_RIGHT : 'Could not update right to the server', PREREQUISITE_ENTER_TITLE : 'Enter the title of the prerequisite', PREREQUISITE_CHOOSE_TOPIC : 'Or choose a note folder of a required note folder', NONE : 'No access', ALREADY_SHARED_WITH_ALL_GROUPS : 'You have already shared this note folder with all groups you are member of.', LICENSE_NO_RESERVED : 'Public Domain, no rights reserved', LICENSE_BY : 'Attribution (by)', LICENSE_BY_SA : 'Attribution Share Alike (by-sa)', LICENSE_BY_ND : 'Attribution No Derivatives (by-nd)', LICENSE_BY_NC : 'Attribution Non-commercial (by-nc)', LICENSE_BY_NC_SA : 'Attribution Non-commercial Share Alike (by-nc-sa)', LICENSE_BY_NC_ND : 'Attribution Non-commercial No Derivatives (by-nc-nd)', COULD_NOT_CHANGE_TOPIC_LICENSE : 'Could not change the license of the note folder', LICENSE_TEXT : 'License', LICENSE_EXPLANATION : 'Detailed explanation...', LICENSE_EXPLANATION_LINK : 'http://creativecommons.org/about/license/', LICENSE_HEADER : 'Content license', LICENSE_APPLICATION : 'The license applies as soon as this content is shared with members of a group.', NO_RECENT_ACTIONS : '(History empty)', NO_CARD_SELECTED : 'You didn\'t select a note sheet . Click on "Select note sheet" to do so.', BALLOON_TEXTS : {
                        DESKTOP : {
                           CLIPBOARD_EXPLANATION : '<h3>Tag clipboard</h3><p>This is where removed tags are moved to. Some paper in the box indicates that there are removed tags.</p>', HIGHLIGHTER_EXPLANATION : '<p>Now, <strong>highlight some text</strong> on the note sheet</p>', NO_TAGS_EXPLANATION : '<h3>Card box</h3><p>There are <strong>no flash cards</strong> in the card box so far.</p><p>Use the marker to <strong>highlight notes</strong> and insert questions.</p>', NO_TAGS_HERE_EXPLANATION : '<h3>Memorize note folder</h3><p>There are <strong>no flash cards on this note sheet</strong>.</p><p>Use the marker, to <strong>highlight notes</strong> and insert questions.</p><p><a title="Opens the card box to memorize current note folder" class="mem" href="javascript:void(0)">Memorize whole note folder</a>.</p>', CHOOSE_SCOPE : '<h3>Memorize note sheets</h3><p>Choose the <strong>range of note sheets</strong> you want to memorize.</p><div class="options"></div>'}
                        , EDITOR : {
                           TOO_MUCH_CONTENT : '<h3>Content exceeds note sheet size</h3><p>To make this note sheet easier to get, <strong>keep it short</strong>.</p><p class="add-hint">Insert <strong>further note sheets</strong> for the additional content using the page flip button.</p>'}
                        , TRAINER : {
                           ANSWER_UP : '<h3>Flash card moved up</h3><p>Since you have answered the qestion <strong>correctly</strong>, the flash card has been moved <strong>one level up</strong>.</p>', ANSWER_REVERT : '<h3>Flash card moved back</h3><p>Since you have rated your answer as <strong>incorrect</strong>, the flash card has been moved <strong>back to the first level</strong>.</p>', ANSWER_DOWN : '<h3>Flash card moved down</h3><p>Since you <strong>did not know the answer</strong> to the question, the flash card has been moved <strong>one level down</strong>.</p>', ANSWER_REMOVED : '<h3>Removed flash card</h3><p>Since you answered the question correctly <strong>four times in a row</strong>, the flash card has been removed <strong>from this training session</strong>.</p>'}
                        }
                     , IMAGE_EDITOR : "Image editor", REVERT_CHANGES : "Revert all changes to this note sheet", REVERT_CHANGES_TEXT : "All changes to this note sheet and its tags will be lost. Do you want to proceed?", EMPTY_TRASH : "Empty tag clipboard", MOVE_TO_TRASH : "Move to tag clipboard", INSERT_FROM_TRASH : "Insert from tag clipboard", CARD_UPDATE_PENDING : "Your notes are being saved. If you abort the process of saving, all changes will be lost.", CARD_UPDATE_FAILURE : "Uploading of your notes about #### failed. Would you like to try again?", ABORT_UPLOAD : "Abort update and revert changes", DISMISS_DATA : "Revert changes", RETRY_UPLOAD : "Retry saving", CARD_WAS_DELETED : "Karte wurde gelöscht.", COULD_NOT_GET_TOPIC_REVISION : "Topic Revision konnte nicht geladen werden", RESET_FONT : "Reset font", NOTIFICATION_MESSAGES : ["A friend of you has a problem. Click here to help out."], NOTIFICATION_TITLES : ["New Problem"], NEEDS_SHARING_TO_GET_SOLVED_HTML : "<strong>This tag is not visible to other users</strong> because the note folder is not shared with other users. Click on the community button to share it.", PASTE_CURRENT_SELECTION : "Paste selection from note sheet", NOTIFICATIONS : "News from my groups", OPEN_INVITATIONS_HTML : "You have <span/>&nbsp;group invitations.", OPEN_REQUESTS_HTML : "You've requested <span/>&nbsp;memberships.", OPEN_OTHERS_REQUESTS_HTML : "There are <span/>&nbsp;membership requests.", QUESTION_REQUESTS_HTML : "There are <span/>&nbsp;unsolved problems.", EMPTY_PROBLEM : "(No question available)", FIND_FRIENDS_HEADER : "Search groups", FIND_FRIENDS_MORE : "more...", SHARING_SUB_HEADER : "Sharing", SHARING_MORE : "more...", SHARED_WITH : "Note folder is shared", UNSHARED : "Note folder not shared yet", SHARED_WITH_YOU : "Note folder shared with you", EDIT_SHARING_HEADER : "Share and license current note folder", VIEW_SHARING_HEADER : "View sharing info and license", GROUP_FINDER_HEADER : "Search in groups", FIND_BUDDIES_HTML : "<p><strong>Use the search field</strong> to find study fellows, friends or groups.</p>", NO_GROUP_RESULTS_HTML : "<p><strong>No groups were found.</strong> Try a less specific search query.</p>", COULD_NOT_FIND_BUDDIES_QUESTION : "Could not find your fellows? Create a new group and invite them!", NEWS : "Notifications", COULD_NOT_RETRIEVE_NOTIFICATION_MESSAGE : "Could not retrieve notifications.", NO_NOTIFICATIONS_HTML : "<p><strong>No notifications</strong>.</p>", COULD_NOT_HIDE_MESSAGE : "Could not mark the message as read.", INSERT_PAGE : "Insert sheet", START_TOPIC : "New note folder", BACK : "Back", FLIP_PAGE : "Flip sheet", CREATE_NEW_TOPIC : "New note folder", CREATE_NEW_TOPIC_EXPLANATION : 'Closes current note folder and creates a new one', CLEANUP_TOPIC : 'Cleanup note folder', CLEANUP_TOPIC_EXPLANATION : 'Removes empty note sheets from the note folder', PROGRESS_HEADER : "Current note folder", NEW_MORE : "new...", NEW_TOPIC_EXPLANATION : "Open new note folder", ADJUST_MORE : "edit...", ADJUST_EXPLANATION : "Manage the current note sheets, arrange them and insert new ones", COULD_NOT_CHECK_REVISION : "Could not check for new version of this note sheet", COULD_NOT_SYNC_TRAINING : "Training data could not be synchronized", TRASH_EMPTY : "The tag clipboard is currently empty", NO_CONTENT_FOR_HIGHLIGHT : "The marker cannot be used on an empty note sheet. Take some notes first.", NO_CONTENT_FOR_REINSERT : "Removed tags cannot be re-inserted on an empty note sheet.", MANAGE_TRASH : "Manage tag clipboard", INSERT_TRAINABLE_MARKERS : "To memorize this note sheet, you should insert definitions, questions or problems. These allow to link your questions with answers in the context of the related part of the text.", DO_MORE_FLASH_CARDS : "In order to properly memorize what is on this note sheet, you should insert more tags first.", SEARCH_MY_TOPICS_HEADER : "Search and manage note folders", FIND_MY_TOPIC_HTML : "<p><strong>Use the search field</strong> to search available note folders</p>", NO_RESULTS_OF_MY_TOPIC_SEARCH_HTML : "<p><strong>None of your note folders matches your query.</strong> Try a less specific search query.</p>", SEARCH_ARTICLES_HEADER : "Search and import articles", FIND_SEARCH_HTML : "<p><strong>Use the search field</strong> to search and view Wikipedia articles and note sheets shared by others.</p>", NO_RESULTS_HTML : "<p><strong>No items matched your request.</strong> Try a less specific search query.</p>", SEARCH_MORE : "more...", DRAG_TO_LEFT_EXPLANATION : "Select some text and drag it onto the note sheet.", VIEW_RESULT_EXPLANATION : "Search result shown below.", COULD_NOT_LOAD_CARD : "Could not retrieve the selected note sheet", DO_NOT_SHOW_AGAIN : "Do not show again", WIKI_AJAX_URL : "http://en.wikipedia.org/w/api.php?action=opensearch&search=", SEARCH_WIKIPEDIA : 'Search Wikipedia', REMOVE_ALERT : "hide", GROUP_ACCESS_READONLY : "Read only", GROUP_ACCESS_WRITE : "Modify", GROUP_ACCESS_STRUCTURE : "Modify and extend", GROUP_ACCESS_OWNER : "Full access", WIKI_URL : "http://en.wikipedia.org/w/index.php?title=", MAP_VIEW_EXPLANATION : "View current note folder", COMMUNITY_EXPLANATION : "Connect with your fellow students and share your work", SEARCH_EXPLANATION : "Search your note sheets and import articles", MEMORIZE_EXPLANATION : "Memorize tags using card boxes", CLIPBOARD_EXPLANATION : "Re-insert removed tags", INSERT_EXPLANATION : "Insert links, images and other multimedia content", PROBLEM_LIST_HEADING : "Questions and answers", PROBLEM_LIST_HEADERS : ['Question', 'State', 'Buddy', 'Group'], ANSWERED : "Answered", UNSOLVED : "Unsolved", YOU : "You", BY_YOU : "you", INVITE_LIST_HEADERS : ['Buddy', 'Email'], SHARED_BY : "By", GROUP_REQUESTED_FROM_TOPIC : "An admin has to confirm your request. You can view the shared note folder as soon as you've joined the group.", JOINED_GROUP : "Group was joined", CARD_LOCKED : "<strong>The note sheet is being edited</strong> by another user right now and can't be changed.", STAY_READ_ONLY : "Continue read-only", CHECK_OUT_TRY_AGAIN : "Try again", CREATE_ADVANCED : "Take advanced notes related to the current note sheet", SWITCH_ADVANCED : "Show advanced notes", SWITCH_MAIN : "Show essential notes", CREATE_ILLUSTATIVE : "Take illustrative notes related to the current note sheet", SWITCH_ILLUSTATIVE : "Show illustrative notes", MEMORIZE_ALL : "Whole note folder", MEMORIZE_STUDIED : "Sheets studied in this session", MEMORIZE_CURRENT : "Current sheet", FIND_GROUP : "Find groups", MINIMIZE : "Minimize", MAXIMIZE : "Maximize", COULD_NOT_LOAD_GROUPS : "Could not load groups", REFER_FLOAT_CONTEXT : "(Selected notes shown on the context panel)", LATEX_EDITOR : "Equation editor, LaTeX", BLOCK_MODE : "block mode", RESET_SIZE : "Reset size", HELP : "Help...", LATEX_HELP_LINK : "http://www.mediabird.net/en/about/latex", UNKNOWN : "Unknown", PROBLEM_SOLVED : 'Problem solved!', AUTHOR : 'Author', INVITATION : "Invitation", REQUEST_BY : "Request by", INSERT_TABLE : "Table", EXTERNAL_REFERENCE : "External reference", CLICK_TO_FOLLOW : 'Click to follow reference', REFERENCES : "References", RELATIONS_LIST_HEADERS : ['Location', 'Action'], EDIT_REFERENCE : 'Edit reference', QUICK_REFERENCE : "Insert quick reference", LINK_CURRENT : "To current page", LINK_CURRENT_EXPLANATION : "Adds a reference to the page that is displayed outside of the notepad", LINK_CURRENT_NOTE : "To notes in panel", LINK_CURRENT_NOTE_EXPLANATION : "Adds a reference to the note sheet displayed on the right", LINK_CURRENT_WIKI : "To Wikipedia article", LINK_CURRENT_WIKI_EXPLANATION : "Adds a reference to the Wikipedia article displayed on the right", REF_EXISTS : "Reference with same location already added", ERROR_REF_EMPTY : "Location or title missing", LOGOUT : 'Log out', OPEN_SHEET : "Open sheet ", ALERT_HEADER : "Info", GIVE_US_FEEDBACK : "Give us some feedback", SEND_FEEDBACK : "Send feedback", SEND : "Send", MESSAGE_EMPTY : "Your message is empty", THANKS_FEEDBACK : "<h3>Thanks for your feedback.</h3><p>We will deal with it asap.</p>", FEEDBACK_ERROR : "Could not send your feedback", FOLLOW_LINK : "Follow link", EDIT_LINK : "Edit link", INSERT_COLUMN : "Insert column", INSERT_ROW : "Insert row", DELETE_COLUMN : "Delete column", DELETE_ROW : "Delete row", TOPIC_COULD_NOT_BE_UPDATED : "The note folder layout could not be updated. You cannot delete a note sheet being edited by someone else.", DELETE_TOPICS : "Delete checked note folders", DELETE_TOPICS_CONFIRM : "The following note folders will be deleted:\n####\n\nAre you sure you want to proceed?", COULD_NOT_DELETE_TOPICSS : "Could not delete the chosen note folders.", MANAGE_TOPICS : "All note folders...", SWITCH_TO_FULL : "View note sheet with all advanced features", TOPICS_SHARED_WITH_GROUP : "Note folders shared with group", GROUP_TOPIC_LIST_HEADERS : ["Note folder", "Creator", "Access"], MAX_NO_OF_CARDS_IN_TOPIC : "Note folder full. The note folder contains the maximum number of note sheets possible. Create a new note folder or arrange your notes differently.", NO_GROUP_CHANGES : "No changes since last save.", CHECK_TOPICS_TO_DELETE : "In the list above, tick the note folders that you wish to delete", HISTORY : "Recent note folders", GO_BACK_TO : "Go back to", NAVI_RIGHT_ADD_EXPLANATION : "Add a blank note sheet and view it", NAVI_RIGHT_EXPLANATION : "View next note sheet", NAVI_LEFT_EXPLANATION : "View previous note sheet", REPETITION : "Repetition", OPTIONAL : "optional", SHARE : "Share", YOUR_QUESTION : "Your question"}
                  client.lang.de = {
                     code : 'de', _H1 : 'Überschrift 1', _H2 : 'Überschrift 2', _H3 : 'Überschrift 3', ADD_LEFT : 'Notizzettel links einfügen', ADD_RIGHT : 'Notizzettel rechts einfügen', ADD_TRANSLATION : 'Einfügen', ANNOTATION : 'Anmerkung', ANNOTATE : 'Anmerken', ANSWER : 'Antwort', CORRECT_TRANSLATION : 'korrekte Übersetzung ', ANSWER_MODE_BY_RANGE : 'Auswahl verwenden', ANSWER_MODE_EXPLICIT : 'Eingeben', ANSWER_MODE : 'Antwortmodus', ANSWER_RIGHT : 'Wusste ich', ANSWER_TOO_LONG : 'Antwort ist zu lang.', ANSWER_UNSURE : 'Später wiederholen', ANSWER_WRONG : 'Nicht gewusst', BOLD : 'Fett', BOX_LEVEL_BRIDGE : ' auf Kastenniveau ', CANCEL : 'Abbrechen', CAPTCHA : 'Sicherheitscode', CAPTCHA_MISSING : 'Tippe den Sicherheitscode ab und trage ihn links ein', CARD_EMPTY : '(Dieser Notizzettel ist leer)', NOTE_SHEET : 'Notizzettel', CATEGORY : 'Stichwörter', CLICK_TO_ENTER : '(Klicke hier, um dir Notizen zu machen)', CLICK_TO_ENTER_ADVANCED : '(Klicke hier, um dir weiterführende Notizen zur Hauptebene zu machen)', CLICK_TO_ENTER_ILLUSTRATIVE : '(Klicke hier, um deine bisherigen Notizen zu veranschaulichen)', ILLUSTRATION_OF : 'Veranschaulichung von ', MORE_DETAILS_ON : 'Details zu ', CLOSE : 'Schließen', CONFIRM_CLOSE : 'Wenn du bestätigst, gehen alle nicht gespeicherten Änderungen verloren.', CONFIRM_REQUEST_SENT : 'Eine Bestätigungsmail wurde an deine Email-Adresse versandt.', ACCOUNT_CREATED : 'Das Konto wurde erfolgreich eingerichtet, du kannst dich nun einloggen.', CONNECTION_DOWN : 'Die Verbindung konnte nicht aufrechterhalten werden. Versuche erneut zu laden.', CONNECTION_ERROR : 'Die Verbindung ist abgebrochen. Versuche erneut zu laden.', COPY : 'Auswahl kopieren', COPY_SECURITY : 'Kopieren wegen einer Browserbeschränkung nicht möglich. Verwende stattdessen die Tastenkombination Strg+C.', COULD_NOT_UPDATE_TOPIC : 'Mappe konnte nicht aktualisiert werden', COULDNT_COPY_IMAGE : 'Bild konnte nicht kopiert werden', CREATE : 'Erstellen', CUT : 'Auswahl ausschneiden', CUT_SECURITY : 'Ausschneiden wegen einer Browserbeschränkung nicht möglich. Bitte verwende stattdessen die Tastenkombination Strg+X.', DEFINITION : 'Definition', DEFINE_SELECTION : 'Definieren', DEFINITION_QUESTION : 'Wie lautet die Definition für:', DELETE : 'Löschen', DESCRIPTION : 'Beschreibe die neue Funktionalität', MEDIABIRD_DESCRIPTION : 'Bei Mediabird geht\'s darum, gemeinsam zu lernen. Lernende können Notizen zu Themen anlegen, die sie interessieren und Fragen im Kontext des Stoffes diskutieren, indem sie auftreten.</p><p>Gib Benutzernamen und Passwort ein und klicke auf "Anmelden" oder registriere dich mit einem Klick auf "Registrieren".</p>', BROWSER_BAD_TEXT : '<h3>Dein Browser oder die Browserversion wird von Mediabird nicht unterstützt. Aktualisiere deinen Browser oder installiere den von uns empfohlenen Browser <a href="http://www.getfirefox.com" target="_blank">Firefox</a></h3>', EDIT_CARD_FROM_MAP : 'Inhalt bearbeiten', EDIT_EMPTY_CARD_FROM_MAP : 'Inhalt für Notizzettel erstellen', CANNOT_DELETE_WHILE_OPEN : 'Der gerade geöffnete Notizzettel kann nicht gelöscht werden. Öffne einen anderen Notizzettel und versuche es erneut.', CANNOT_DELETE_LAST : 'Kann den letzten Notizzettel nicht löschen. Klicke auf "Alle Notizmappen...", um die Notizmappe zu entfernen.', TRAINER_HEADING : 'Notizmappe wiederholen', EDIT : 'Editieren', EDIT_MAP : '<h1>Sortiere die Notizzettel mit der Maus um.</h1><p>Ziehe sie <strong>hoch und runter</strong>, um ihren Schwierigkeitsgrad festzulegen.</p>', EXTEND_MAP : '<h1>Füge Notizzettel hinzu oder navigiere zu anderen Notizzetteln.</h1><p>Ziehe neue Zettel <strong>hoch und runter</strong>, um ihren Schwierigkeitsgrad festzulegen.</p>', EDIT_MAP_ADD : '<h1>Du bist beim letzten Notizzettel angelangt.</h1><p>Du kannst <strong>weitere Notizzettel</strong> mit dem \'+\'-Symbol hinzufügen.</p>', EDIT_MAP_ADD_LEFT : '<h1>Du bist beim ersten Notizzettel angelangt.</h1><p>Du kannst <strong>weitere Notizzettel</strong> mit dem \'+\'-Symbol hinzufügen.</p>', SHOW_MAP : '<h1>Überblick über den aktuellen Notizordner.</h1>', EDITOR_TITLE : 'Editor', EMAIL : 'Email', EMAIL_MISSING : 'Email fehlt.', INVALID_EMAIL : 'Du hast eine ungültige Email-Adresse eingegeben.', EMPTY_ROW : '(Die Liste enthält keine Elemente)', DESCRIBE_ISSUE : '(Hier klicken, um Problem zu beschreiben)', ENTER_ANNOTATION : '(Hier klicken, um eine Anmerkung einzugeben)', ENTER_ANSWER : '(Hier klicken, um eine Antwort einzugeben)', ENTER_DEFINITION : '(Hier klicken, um eine Definition der Auswahl einzugeben)', SUGGEST_ANSWER : '(Hier klicken, um eine Antwort auf die Frage vorzuschlagen)', ENTER_EXPRESSION : '(Hier klicken, um zur Auswahl zugehörigen Ausdruck zu definieren)', ENTER_NEW_CARD_TITLE : 'Gib einen neuen Namen für den Notizzettel ein.', ENTER_QUESTION : '(Hier klicken, um eine Frage einzugeben)', ERROR_CHANGING_STATUS : 'Die Zugriffsrechte für diese Gruppe konnte nicht aktualisiert werden. Versuche es noch einmal.', ERROR_CREATING_USER : 'Bei Anlegen des neuen Benutzer-Accounts ist ein Fehler aufgetreten', ERROR_FUNCTION_DISABLED : 'Die Funktion wurde durch den Administrator deaktiviert.', ERROR_LOADING_TRAINING : 'Die letzte Ladeoperation ist fehlgeschlagen.', ERROR_MOVE : 'Fehler beim Hochladen der Datei.', ERROR_NO_FILE : 'Es wurde keine Datei hochgeladen.', ERROR_TYPE : 'Der Dateityp ist unzulässig. Bitte wähle nur Bild-Dateien aus.', ERROR_QUOTA : 'Du hast das Speicherlimit deines Accounts erreicht.', ERROR_SENDING_CONFIRM : 'Beim versenden der Bestätigungsemail ist ein Fehler aufgetreten.', ERROR_TOO_BIG : 'Das ausgewählte Bild ist zu groß.', ERROR_WHILE_UPLOADING : 'Das Bild konnte nicht hochgeladen werden.', FILE : 'Datei', FLASH_CARD_BOXES : 'Karteikästen', FIND_HOW_TO : 'Benutze Strg+F, um in in der aktuellen Notizzettel zu suchen.', FIND : 'Finden...', FLOAT_LEFT : 'Rechts umbrechen', FLOAT_NONE : 'Nicht umbrechen', FLOAT_RIGHT : 'Links umbrechen', FLOAT_STYLE : 'Textfluss', FORMAT : 'Format', FORMULA_CONTAINS_BLACKLISTED : 'LaTeX Code enthält nicht erlaubte Tags', FORMULA_TOO_BIG : 'Formel zu groß, um dargestellt zu werden', FORMULA_TOO_LONG : 'Formel zu lang', GENERAL_PREFIX : 'Überlege dir die Antwort zur Fragekarte aus der Notizzettel ', GET_NEW_CAPTCHA : 'Neuen Code generieren', GET_PASSWORD : 'Passwort vergessen', GHOST_ENTRY : '(hier klicken, um ein Element hinzuzufügen)', GHOST_TOPIC : '(hier klicken, um neue Thema hinzuzufügen)', GO : 'Los', GOTO_ANSWER : 'Antwort zeigen', GOTO_QUESTION : 'Frag nochmal', SHOW_CONTEXT : 'Kontext zeigen', CONTEXT : 'Kontext', GROUP : 'Gruppe', HEIGHT : 'Höhe', HIGHLIGHT : 'Hervorheben', HTML_CODE : 'HTML Code', CODE : 'Code', IMP_LEVEL : 'Als wichtig markiert', IMP_LEVEL_BY : 'Als wichtig markiert von', MARK_IMPORTANT : 'Als wichtig markieren', IN : 'in', INDENT : 'Mehr einrücken', INSERT_HTML : 'HTML', INSERT_CODE : 'Beispielcode', INSERT_IMAGE : 'Bild', INSERT : 'Einfügen', INSERT_LATEX : 'Formel', AUTO_CONVERT_LATEX : 'Automatik', AUTO_CONVERT_EXPLANATION : 'Konvertiert automatisch LaTeX-Code des aktuellen Notizzettels zu Formeln', AUTO_LATEX_NO_JOBS : 'Es wurde kein LaTeX-Code auf dem Notizzettel gefunden', LATEX_SOURCE : 'Formel', INSERT_LINK : 'Link', INSERTORDEREDLIST : 'Nummerierung', INSERTUNORDEREDLIST : 'Aufzählung', INVALID_END : '" ist ungültig', INVALID_START : 'Der Wert von "', IS_IN : 'ist', ISSUE : 'Problem', ASK_QUESTION : 'Frage stellen', ANSWER_LINK : 'Frage einfügen', ITALIC : 'Kursiv', INVITE : 'Einladen', NO_INVITEES_SELECTED : 'Gib an, wen du in die Gruppe einladen möchtest', INVITE_USER : 'Freunde einladen', INVITE_USERS_TO_GROUP : 'Freunde in Gruppe einladen', FOLLOWING_NOT_FOUND : 'Diese Freunde konnten nicht eingeladen werden, da sie Mediabird nicht nutzen: ', NONE_INVITED : 'Deine Freunde wurden bereits eingeladen.', COULD_NOT_INVITE_USERS : 'Die angegebenen Freunde konnten nicht eingeladen werden.', EMAIL_OR_NAME : 'Email-Adressen', NO_USERS_TO_INVITE : '(Wer bereits Mitglied ist, kann nicht mehr eingeladen werden)', FURTHER_USERS_INVITE : 'Per Email-Adresse', EMAIL_SEPARATE_EXPLANATION : 'Email-Adressen durch Kommata trennen.', KNOWN_USERS : 'Bekannte/Freunde', JUST_ENABLED : 'Dein Account ist erfolgreich aktiviert worden. Zum Einloggen, das untere Formular benutzen.', JUSTIFY_CENTER : 'Zentriert', JUSTIFY_FULL : 'Blocksatz', JUSTIFY_LEFT : 'Linksbündig', JUSTIFY_RIGHT : 'Rechtsbündig', LANGUAGES : ['Englisch', 'Afar', 'Abkhazian', 'Afrikaans', 'Akan', 'Albanisch', 'Amharisch', 'Arabisch', 'Aragonesisch', 'Armenisch', 'Assamesisch', 'Avarisch', 'Avestan', 'Aymara', 'Aserbaijanisch', 'Bambara', 'Bashkir', 'Basquisch', 'Belarusisch', 'Bengali', 'Bihari', 'Bislama', 'Bosnisch', 'Bretonisch', 'Bulgarisch', 'Burmesisch', 'Catalanisch', 'Chamorro', 'Chechen', 'Chichewa', 'Chinesisch', 'Kirch-Slavisch', 'Chuvash', 'Kornisch', 'Korsicanisch', 'Cree', 'Kroatisch', 'Tschechisch', 'Dänisch', 'Divehi', 'Dutch', 'Dzongkha', 'Esperanto', 'Estnisch', 'Ewe', 'Faroisch', 'Fijian', 'Finnisch', 'Französisch', 'Fulah', 'Galician', 'Ganda', 'Georgisch', 'Deutsch', 'Griechisch', 'Guarani', 'Gujarati', 'Haitian', 'Hausa', 'Hebräisch', 'Herero', 'Hindi', 'Hiri Motu', 'Ungarisch', 'Isländisch', 'Ido', 'Igbo', 'Indonesisch', 'Interlingue', 'Inuktitut', 'Inupiaq', 'Irisch', 'Italienisch', 'Japanisch', 'Javanesisch', 'Kalaallisutisch', 'Kannadisch', 'Kanuri', 'Kashmirisch', 'Kazakh', 'Khmer', 'Kikuyu', 'Kinyarwanda', 'Kirghiz', 'Kirundi', 'Komi', 'Kongo', 'Koreanisch', 'Kurdisch', 'Kwanyama', 'Lao', 'Latein', 'Latvisch', 'Limburgisch', 'Lingala', 'Lithuanisch', 'Luba-Katanga', 'Luxembourgisch', 'Macedonisch', 'Malagasy', 'Malay', 'Malayalam', 'Maltese', 'Manx', 'Maori', 'Marathi', 'Marshallese', 'Moldavisch', 'Mongolisch', 'Nauru', 'Navajo', 'Ndonga', 'Nepalisch', 'Nord Ndebele', 'Nord Sami', 'Norwegisch', 'Norwegisch Bokmal', 'Norwegisch Nynorsk', 'Occitanisch', 'Ojibwa', 'Oriya', 'Oromo', 'Ossetisch', 'Pāli', 'Panjabi', 'Pashto', 'Persisch', 'Polnisch', 'Portugisisch', 'Quechua', 'Raeto-Romanisch', 'Romänisch', 'Russian', 'Samoan', 'Sango', 'Sanskrit', 'Sardinisch', 'Gälisch', 'Serbisch', 'Serbo-Croatian', 'Shona', 'Sichuan Yi', 'Sindhi', 'Sinhala', 'Slovakisch', 'Slovenisch', 'Somalisch', 'Sotho', 'Süd Ndebelisch', 'Spanish', 'Sundanesisch', 'Swahili', 'Swati', 'Schwedisch', 'Tagalog', 'Tahitian', 'Tajik', 'Tamil', 'Tatar', 'Telugu', 'Thai', 'Tibetanisch', 'Tigrinya', 'Tonga', 'Tsonga', 'Tswana', 'Türkisch', 'Turkmenisch', 'Twi', 'Uighur', 'Ukrainisch', 'Urdu', 'Uzbekisch', 'Vendisch', 'Vietnamesisch', 'Volapuk', 'Walloon', 'Walisisch', 'Western Frisian', 'Wolof', 'Xhosa', 'Jiddisch', 'Yoruba', 'Zhuang', 'Zulu', 'Latein', 'Altgriechisch', 'Hocharabisch'], LEVEL : 'Ebene', LEVEL_IMPORTANT : 'Wichtig', LEVEL_ADVANCED : 'Weiterführend', LEVEL_ILLUSTRATIVE : 'Veranschaulichend', LINK_HEADING : 'Link-Editor', LINK_TYPE : 'Link-Typ', LINK_REQUIRES_SELECTION : 'Zum Einfügen eines Links ist eine Textauswahl notwendig', COLLAPSIBLE_REQUIRES_SELECTION : 'Zum Einfügen eines Klappblocks ist eine Textauswahl notwendig', LOCATION : 'Ziel', LOGON : 'Anmelden', COULD_NOT_CLOSE_SESSION : 'Sitzung konnte nicht geschlossen werden. Zum Start einer neuen Sitzung die Seite neuladen.', SET_ACCESS_NONE : 'Kein Zugriff', SET_ACCESS_READ : 'Lesezugriff', SET_ACCESS_WRITE : 'Schreibzugriff', SET_ACCESS_STRUCTURE : 'Voller Zugriff', TOO_MANY_STEPS : 'Maximale Anzahl von Notizzetteln in der Mappe erreicht. Sortiere Notizzettel um oder lege eine neue Mappe an.', MOVE_LEFT : 'Nach links', MOVE_RIGHT : 'Nach rechts', NAME : 'Name', NAME_MISSING : 'Benutzername fehlt', NO_ANSWER : 'Du hast keine Antwort eingegeben.', NO_ANNOTATION : 'Du hast keine Anmerkung eingegeben.', NO_DEFINITION : 'Du hast keine Definition eingegeben.', NO_QUESTION : 'Du hast keine Frage eingegeben.', NO_EXPRESSION : 'Du hast keinen Ausdruck eingegeben.', NO_CARD_TITLE : '(nichts ausgewählt)', NO_FLASH_CARDS : 'Keiner der Notizzettel in dieser Mappe enthält Fragekarten. Es kann nichts geübt werden.', NO_HIGHLIGHT_IN_EMPTY_CARD : 'Hervorheben in leeren Notizzetteln nicht möglich', NO_URL_GIVEN : 'Um ein Bild aus einer URL einzufügen, muss ein Adresse angegeben werden.', NO_MARKER_FOR_DEL : 'Wähle das Tag aus, das du als erstes löschen möchtest', NOCAT : '(Keine Stichwörter)', NOT_CONFIRMED : 'Bitte bestätige deine Email-Adresse, durch klicken auf den Link in der dir zugesendeten Bestätigungsemail.', OK : 'OK', OTHER_ERROR : 'Fehler bei Skriptzugriff.', OUTDENT : 'Weniger einrücken', PADDING_BOTTOM : 'Unten', PADDING_LEFT : 'Links', PADDING_RIGHT : 'Rechts', PADDING_STYLE : 'Abstand', PADDING_TOP : 'Oben', PARA : 'Absatz', PASS_MISSING : 'Password fehlt', PASS_NOT_MATCHING : 'Passwörter stimmen nicht überein', PASSWORD_ACCESS_KEY : 'P', PASSWORD_ERROR : 'Es ist ein Fehler bei der Passwortabfrage aufgetreten.', PASSWORD_INVALID : 'Es ist ein Fehler beim Senden des Passworts an die angegebene Email-Adresse aufgetreten.', PASSWORD : 'Passwort', PASSWORD_AGAIN : 'Wiederhole Passwort', PASSWORD_NO_USER : 'Die eingegebene Email-Adresse passt zu keinem existierenden Account.', PASSWORD_SENT : 'Eine Email mit deinem Passwort wurde an die zum Account gehörende Email-Adresse geschickt.', PASTE : 'In Auswahl einfügen', PASTE_SECURITY : 'Einfügen wegen einer Browserbeschränkung nicht möglich. Verwende stattdessen die Tastenkombination Strg+V.', PLEASE_TRANSLATE : 'Übersetzung in ', QUESTION_EDIT : 'Editiere die ', QUESTION : 'Frage', QUESTION_MODE : 'Tag-Typ', QUESTION_TOO_LONG : 'Frage ist zu lang.', QUESTION_VIEW : 'Ansehen der ', REDO : 'Wiederholen', REFERENCE : 'Verweisen', REMOVE_FORMAT : 'Formatierung aufheben', REMOVE_LINK : 'Link entfernen', REMOVE : 'Entfernen', REMOVE_THIS : 'Tag löschen', REMOVE_TRANSLATION : 'Entfernen', RENAME : 'Umbenennen', RENDERING_FAILED : 'Formel konnte nicht gerendert werden.', REP_LEVEL : 'Zur Wiederholung vorgemerkt', REP_MARKER : 'Später wiederholen', SHARE : 'Freigeben', REPEAT : 'Wiederholen', REPEAT_TITLE : 'Die Auswahl ist zum Wiederholen markiert.', REVIEW_RIGHT : 'Gewusst', REVIEW_UNSURE : 'Später entscheiden', REVIEW_WRONG : 'Später wiederholen', SEARCH : 'Suche', SEL_REQUIRED_HIGHLIGHT : 'Vor dem Hervorheben den Textteil markieren, der hervorgehoben werden soll.', SELECT_CARD_EXPLANATION : 'Klicke auf einen Notizzettel und wähle "Notizzettel auswählen".', SELECT_CARD : 'Notizzettel auswählen', SELECTED_TEXT : 'Auswahl', SET : 'Setzen', SESSION_CREATION_ERROR : 'Beim Anlegen der Sitzung ist ein Fehler aufgetreten.', SESSION_FINISHED : 'Üben abgeschlossen! In dieser Sitzung sind keine weiteren Fragekarte übgrig.', SESSION_ABORTED : 'Das Laden wurde abgebrochen', SESSION_INVALID : 'Eine ungültige Antwort wurde gesendet. Grund könnte ein Fehler oder eine Zeitüberschreitung sein. Versuche die Seite neuzuladen', SIGN_UP : 'Registrieren', STRIKE_THROUGH : 'Durchstreichen', STUDY_CARD_FROM_MAP : 'Notizzettel anzeigen', STUDY_TOOL_TITLE : 'Viewer', TERMS_ACCEPT : 'Ich habe die <a href="terms.php" target="_blank">Benutzerbedingungen</a> gelesen und verstanden.', TERMS : 'Nutzungsbedingungen', TERMS_MISSING : 'Du hast die Nutzungsbedingungen akzeptiert', TITLE : 'Titel', TOPIC_BRIDGE : ', Mappe ', TOPIC : 'Mappe', TOPIC_EMPTY : 'Die Mappe enthält keine Notizzettel und kann daher nicht geöffnet werden.', TOPICS : 'Mappenliste', TRANSLATE_INTO : 'Übersetzen in', TRANSLATION_EDITOR : 'Übersetzungseditor', TRANSLATION_INTO : 'In', TRANSLATION : 'Übersetzung', TRANSLATE : 'Übersetzen', TRANSLATION_MARKER : 'Übersetzung editieren', TRANSLATION_VIEWER : 'Übersetzung ansehen', TYPE_CARD : 'Notizzettel', TYPE_EMAIL : 'Email-Adresse', TYPE_URL : 'URL', UNDERLINE : 'Unterstreichen', UNDO : 'Rückgängig', UNTITLED : 'Unbenannt', UPDATE : 'Aktualisieren', UPLOAD_BUTTON : 'Hochladen', PICTURE : 'Bild', URL_INVALID : 'Eingegebene URL ist ungültig', USER_EMAIL_TAKEN : 'Die von dir eingegebene Email-Adresse wird bereits von einem anderen Benutzerkonto verwendet.', USER_NAME_TAKEN : 'Der ausgewählte Benutzername wird bereits von einem anderen Benutzerkonto verwendt.', WIDTH : 'Breite', WRONG_PASSWORD : 'Die Kombination von Benutzername und Passwort ist falsch.', WRONG_SECURITY : 'Der eingegeben Sicherheitscode ist falsch', INSERT_COLLAPSIBLE_NOTE_TEXT : '(Text hier eingeben)', INSERT_COLLAPSIBLE : 'Klappblock', ADD_PREREQUISITE : 'Grundlage hinzufügen', REMOVE_PREREQUISITE : 'Grundlage löschen', PREREQUISITES : 'Grundlagen', NO_TITLE_GIVEN : 'Du hast keinen Titel für die Grundlage eingegeben', NO_TOPIC_SELECTED : 'Du hast kein Mappe als Grundlage ausgewählt', PREREQUISITE : 'Grundlage zum Verständnis dieses Themas', EDIT_FIRST_CARD_OF_PREREQUISITE : 'Ersten Notizzettel der Grundlage editieren', STUDY_PREREQUISITE : 'Grundlage lernen', IMAGE_ADJUST_HEADING : 'Bildeigenschaften anpassen', IMAGE_UPLOAD_HEADING : 'Neues Bild hochladen', IMAGE_LOCATION_HEADING : 'Folgende URL nehmen', COULD_NOT_LOAD_TOPICS : 'Mappenliste konnte nicht abgefragt werden', COULD_NOT_LOAD_GROUPS : 'Gruppenliste konnte nicht abgefragt werden', CREATE_GROUP : 'Gruppe erstellen', VIEW_MY_GROUPS : 'Meine Gruppen anzeigen', REQUEST_LIST_HEADER : 'Einladungen in Gruppen und Mitgliedschafts-Anfragen', JOIN_GROUP : 'Gruppe beitreten', REQUEST_GROUP : 'Mitgliedschaft anfragen', LEAVE_GROUP : 'Gruppe verlassen', ACCEPT_MEMBER : 'Zulassen', REJECT_MEMBER : 'Ablehnen', PENDING : 'Ausstehend', INVITED : 'Eingeladen', REQUESTED : 'Angefragt', MEMBER_LIST_HEADERS : ['Name', 'Level'], REQUESTS_LIST_HEADERS : ['Gruppe', 'Info', 'Aktionen'], SET_TO : 'Setzen auf', ADMIN_LEVEL : 'Admin', MEMBER_LEVEL : 'Mitglied', EDIT_GROUP_HEADER : 'Gruppe editieren', VIEW_GROUP_HEADER : 'Gruppe ansehen', GROUP_DESCRIPTION : 'Beschreibung', GROUP_NAME : 'Name', GROUP_CATEGORY : 'Stichwörter', GROUP_ACCESS : 'Zugriff für Nicht-Mitglieder', GROUP_NO_ACCESS : 'Nicht anzeigen', GROUP_VIEW_ACCESS : 'Anzeigen, Anfragen erlauben', GROUP_JOIN_ACCESS : 'Anzeigen, Beitritt erlauben', COULD_NOT_UPDATE_GROUP : 'Gruppe konnte nicht aktualisiert werden', MEMBER_LIST : 'Mitglieder', COULD_NOT_SET_MEMBER_ACCESS : 'Gruppenzugriff konnte nicht geändert werden', COULD_NOT_LEAVE_GROUP : 'Gruppe konnte nicht verlassen werden', COULD_NOT_JOIN_GROUP : 'Gruppe konnte nicht beigetreten werden', COULD_NOT_DELETE_GROUP : 'Gruppe konnte nicht gelöscht werden', CANNOT_REMOVE_NONEMPTY_GROUP : 'Eine Gruppe, die immer noch Mitglieder hat kann nicht gelöscht werden. Entferne zuerst die Benutzer.', ACCEPT_INVITATION : 'Annehmen', REJECT_INVITATION : 'Ignorieren', CANCEL_REQUEST : 'Anfrage löschen', COULD_NOT_REMOVE_REQUEST : 'Antrag konnte nicht entfernt werden', COULD_NOT_ACCEPT_REQUEST : 'Mitgliedsantrag konnte nicht akzeptiert werden', COULD_NOT_REJECT_REQUEST : 'Mitgliedsantrag konnte nicht abgelehnt werden', COULD_NOT_REMOVE_MEMBER : 'Miegliedschaft konnte nicht entfernt werden', CANNOT_LEAVE_GROUP_FROM_EDITOR : 'Verwende die Gruppenansicht, um beigetretenen Gruppen zu verlassen.', REMOVE_MEMBER : 'Aus der Gruppe entfernen', TOPIC_LIST_RECENT_HEADING : 'Verlauf', GROUPLIST_HEADINGS : ['Gruppe', 'Berechtigung'], GROUPLIST_HEADER : 'Freigaben', CLICK_TO_CHANGE_ACCESS : 'Klicke, um die die Berechtigungen der Gruppenmitglieder anzupassen', ADD_GROUP : 'Notizmappe freigeben', SHARE_FOLDER : 'Freigaben verwalten', SHARE_FOLDER_EXPLANATION : 'Verwaltet Freigaben der aktuellen Notizmappe', SHARE_TOPIC_WITH_GROUP : 'Neue Freigabe für aktuelle Notizmappe', FILE_AS : "Notizmappe benennen", MEMBER_RIGHTS : 'Mitglieder dürfen', RIGHT_READONLY_PRESET : 'Anschauen', RIGHT_WRITE_PRESET : 'Anschauen und bearbeiten', RIGHT_STUCTURE_PRESET : 'Anschauen, bearbeiten und strukturieren', NOT_MEMBER_OF_ANY_GROUP : 'Du kannst keine Mappe freigeben, solange du kein Mitglied einer Gruppe bist. Klicke auf "Gruppe erstellen".', COULD_NOT_UPDATE_RIGHT : 'Konnte die Berechtigungen nicht auf dem Server aktualisieren', PREREQUISITE_ENTER_TITLE : 'Gib den Titel der Grundlage ein', PREREQUISITE_CHOOSE_TOPIC : 'Oder wähle eine Mappe mit einenm grundlegenden Thema aus', NONE : 'Kein Zugriff', ALREADY_SHARED_WITH_ALL_GROUPS : 'Du hast diese Mappe bereits allen Gruppen freigegeben, in denen du Mitglied bist.', LICENSE_NO_RESERVED : 'Public Domain, keine Rechte vorbehalten', LICENSE_BY : 'Namensnennung (by)', LICENSE_BY_SA : 'Namensnennung - Weitergabe unter gleichen Bedingungen (by-sa)', LICENSE_BY_ND : 'Namensnennung - keine Bearbeitung (by-nd)', LICENSE_BY_NC : 'Namensnennung - nicht-kommerziell (by-nc)', LICENSE_BY_NC_SA : 'Namensnennung - nicht-kommerziell, Weitergabe unter gleichen Bedingungen (by-nc-sa)', LICENSE_BY_NC_ND : 'Namensnennung - nicht-kommerziell, keine Bearbeitung (by-nc-nd)', COULD_NOT_CHANGE_TOPIC_LICENSE : 'Konnte die Lizenz des Themas nicht ändern', LICENSE_TEXT : 'Lizenz', LICENSE_EXPLANATION : 'Ausführliche Erklärung...', LICENSE_EXPLANATION_LINK : 'http://creativecommons.org/about/license/', LICENSE_HEADER : 'Inhaltslizenz', LICENSE_APPLICATION : 'Die Lizenz ist anwendbar sobald das Thema in einer Gruppe freigegeben wurde.', NO_RECENT_ACTIONS : '(Verlauf leer)', NO_CARD_SELECTED : 'Du hast keine Notizzettel ausgewählt, klicke auf "Wähle Notizzettel" um dies zu tun.', BALLOON_TEXTS : {
                        DESKTOP : {
                           CLIPBOARD_EXPLANATION : "<h3>Tag-Ablage</h3><p>Hier werden <strong>entfernte Tags gespeichert</strong>. Sind entfernte Tags vorhanden, so wird dies durch Papier in der rechten Box angezeigt.</p>", HIGHLIGHTER_EXPLANATION : "<h3>Marker</h3><p>Mit dem Marker kann Text farblich hinterlegt und mit Anmerkungen oder Fragen versehen werden.</p>", NO_TAGS_EXPLANATION : '<h3>Karteikasten</h3><p>Bisher sind <strong>keine Fragekarten im Karteikasten</strong>.</p><p>Verwende den Textmarker, um <strong>Notizen hervorzuheben</strong> und Fragekarten hinzuzufügen.</p>', NO_TAGS_HERE_EXPLANATION : '<h3>Mappe wiederholen</h3><p>Es sind <strong>keine Frage-Tags auf diesem Notizzettel</strong>.</p><p>Verwende den Textmarker, um <strong>Notizen hervorzuheben</strong> und Fragekarten hinzuzufügen.</p><p><a title="Öffnet den Karteikasten zur Wiederholung der Notizmappe" class="mem" href="javascript:void(0)">Wiederhole ganze Notizmappe</a>.</p>', CHOOSE_SCOPE : '<h3>Notizzettel wiederholen</h3><p><strong>Welche Notizzettel</strong> möchtest du wiederholen?</p><div class="options"></div>'}
                        , EDITOR : {
                           TOO_MUCH_CONTENT : "<h3>Inhalt überschreitet Notizzettelgröße</h3><p>Für besseres Verständnis sollte auf Notizzetteln nicht mehr stehen als darauf Platz findet.</p><p class=\"add-hint\">Füge mit der Umblätter-Funktion <strong>weitere Notizzettel</strong> für die zusätzlichen Inhalte ein.</p>"}
                        , TRAINER : {
                           ANSWER_UP : "<h3>Fragekarte hochgestuft</h3><p>Da du die Frage <strong>richtig</strong> beantwortet hast, wurde die Fragekarte, um <strong>ein Level hochgestuft</strong>.</p>", ANSWER_REVERT : "<h3>Fragekarte zurückgesetzt</h3><p>Da du deine Antwort als <strong>falsch</strong> eingestuft hast, ist die Frage <strong>zurück auf das erste Level gestuft worden</strong>.</p>", ANSWER_DOWN : "<h3>Fragekarte heruntergestuft</h3><p>Da du <strong>die Antwort nicht wusstest</strong>, wurde die Fragekarte um <strong>ein Level heruntergestuft</strong>.</p>", ANSWER_REMOVED : "<h3>Fragekarte entfernt</h3><p>Da du die Frage <strong>vier mal in Folge</strong> richtig beantwortet hast, wurde die Fragekarte <strong>aus dem Karteikasten</strong> entfernt.</p>"}
                        }
                     , LATEX_EDITOR : 'Formel-Editor, LaTeX', IMAGE_EDITOR : 'Bild-Editor', REVERT_CHANGES : 'Alle Änderungen an diesem Notizzettel rückgängig machen', REVERT_CHANGES_TEXT : 'Alle Änderungen an dieser Notizzettel werden verworfen. Willst du fortfahren?', EMPTY_TRASH : 'Tag-Ablage leeren', MOVE_TO_TRASH : 'In Tag-Ablage verschieben', INSERT_FROM_TRASH : 'Aus der Tag-Ablage', CARD_UPDATE_PENDING : 'Deine Notizen werden gespeichert. Wenn du diesen Vorgang abbrichst, gehen alle Änderungen verloren.', CARD_UPDATE_FAILURE : 'Konnte die Notizen zu #### nicht speichern. Willst du es nochmal probieren?', ABORT_UPLOAD : 'Speichern abbrechen und Änderungen verwerfen', DISMISS_DATA : 'Änderungen verwerfen', RETRY_UPLOAD : 'Speichern erneut probieren', RESET_FONT : 'Schriftart zurücksetzen', NOTIFICATION_MESSAGES : 'Einer deiner Freunde hat eine Frage. Klicke hier, um zu helfen.', NOTIFICATION_TITLES : 'Neue Fragen', NEEDS_SHARING_TO_GET_SOLVED_HTML : '<strong>Dieser Tag ist für andere nicht sichtbar</strong>, da die Mappe nicht für andere freigegeben ist. Klicke auf den Community Button, um sie freizugeben.', PASTE_CURRENT_SELECTION : 'Auswahl aus Notizzettels einfügen', NOTIFICATIONS : 'Neues von deinen Gruppen', OPEN_INVITATIONS_HTML : 'Du hast <span/>&nbsp;Gruppeneinladungen.', OPEN_REQUESTS_HTML : 'Du willst <span/>&nbsp;Gruppen beitreten.', QUESTION_REQUESTS_HTML : 'Es gibt <span/>&nbsp;ungelöste Fragen.', EMPTY_PROBLEM : "(Keine Frage verfügbar)", OPEN_OTHERS_REQUESTS_HTML : 'Du hast <span/>&nbsp;Mitgliedsanträge.', FIND_FRIENDS_HEADER : 'Gruppen suchen', FIND_FRIENDS_MORE : 'mehr...', SHARING_SUB_HEADER : 'Freigeben', SHARING_MORE : 'mehr...', SHARED_WITH : 'Mappe ist freigegeben', UNSHARED : 'Mappe ist noch nicht freigegeben', SHARED_WITH_YOU : 'Mappe wurde dir freigegeben', EDIT_SHARING_HEADER : 'Aktuelle Mappe freigeben und lizenzieren', VIEW_SHARING_HEADER : 'Freigaben und Lizenzen ansehen', GROUP_FINDER_HEADER : 'In Gruppen suchen', FIND_BUDDIES_HTML : "<p><strong>Verwende das Suchfeld</strong>, um Kollegen, Freunde oder Gruppen zu finden.</p>", NO_GROUP_RESULTS_HTML : "<p><strong>Es wurde keine Gruppe gefunden.</strong> Versuche es mit einem weniger spezifischen Suchbegriff.</p>", COULD_NOT_FIND_BUDDIES_QUESTION : 'Deine Freunde nicht gefunden? Erstelle eine neue Gruppe und lade sie ein!', NEWS : 'Neues', COULD_NOT_RETRIEVE_NOTIFICATION_MESSAGE : 'Es wurde keine Benachrichtigung gefunden.', NO_NOTIFICATIONS_HTML : '<p><strong>Keine Benachrichtigung</strong>.</p>', COULD_NOT_HIDE_MESSAGE : 'Nachricht konnte nicht als gelesen markiert werden.', INSERT_PAGE : 'Notizzettel einfügen', START_TOPIC : 'Neue Mappe anlegen', BACK : 'Zurück', FLIP_PAGE : 'Umblättern', CREATE_NEW_TOPIC : 'Neue Mappe anlegen', CREATE_NEW_TOPIC_EXPLANATION : 'Schließt aktuelle Mappe und legt eine neue an', CLEANUP_TOPIC : 'Mappe aufräumen', CLEANUP_TOPIC_EXPLANATION : 'Entfernt leere Zettel dieser Mappe', PROGRESS_HEADER : 'Fortschritt und Übersicht', PROGRESS_HEADER : "Aktuelle Notizmappe", NEW_MORE : "neu...", NEW_TOPIC_EXPLANATION : "Neue Notizmappe öffnen", ADJUST_MORE : "bearbeiten...", ADJUST_EXPLANATION : "Aktuelle Notizmappe verwalten, Zettel umsortieren und einfügen", COULD_NOT_SYNC_TRAINING : 'Trainingsdaten konnten nicht synchronisiert werden', TRASH_EMPTY : 'Die Tag-Ablage ist zur Zeit leer', NO_CONTENT_FOR_HIGHLIGHT : 'Der Marker funktioniert nicht auf einem leeren Notizzettel, mache erstmal ein paar Notizen.', NO_CONTENT_FOR_REINSERT : 'Entfernte Tags können nicht auf einem leeren Notizzettel wieder eingefügt werden.', MANAGE_TRASH : 'Tag-Ablage verwalten', INSERT_TRAINABLE_MARKERS : 'Um diesen Notizzettel zu memorieren, musst du Definitionen, Fragen und Problemstellungen eingeben. Damit kannst du Fragen und Antworten in den Kontext eines Textes stellen.', DO_MORE_FLASH_CARDS : 'Um diese Notizseite gut memorieren zu können, solltest du noch weitere tags einfügen.', SEARCH_MY_TOPICS_HEADER : 'Notizmappen verwalten und durchsuchen', FIND_MY_TOPIC_HTML : '<p><strong>Nutze die Suche</strong>, um deine Mappen zu durchsuchen</p>', NO_RESULTS_OF_MY_TOPIC_SEARCH_HTML : '<p><strong>Es wurden keine Mappen zu dem Suchbegriff gefunden</strong>. Versuche es mit einem wenigeriger spezifischen Suchbegriff.</p>', SEARCH_ARTICLES_HEADER : 'Artikel suchen und importieren', FIND_SEARCH_HTML : '<p><strong>Nutze die Suche</strong>, um Wikipedia-Artikel und Notizzettel anderer zu durchsuchen und anzuzeigen.</p>', NO_RESULTS_HTML : '<p><strong>Für deinen Suchbegriff konnte nichts gefunden werden.</strong> Versuche es mit einem weniger spezifischen Suchbegriff.</p>', SEARCH_MORE : 'mehr...', DRAG_TO_LEFT_EXPLANATION : 'Wähle Text aus und ziehe ihn auf den Notizzettel.', VIEW_RESULT_EXPLANATION : "Suchergebnis wird unten angezeigt.", COULD_NOT_LOAD_CARD : 'Der ausgewählte Notizzettel konnte nicht gefunden werden', COULD_NOT_CHECK_REVISION : 'Die neueste Version des Notizzettel konnte nicht abgerufen werden', DO_NOT_SHOW_AGAIN : "Nicht erneut zeigen", WIKI_AJAX_URL : "http://de.wikipedia.org/w/api.php?action=opensearch&search=", SEARCH_WIKIPEDIA : 'Bei Wikipedia gucken', REMOVE_ALERT : "ausblenden", GROUP_ACCESS_READONLY : "Nur lesen", GROUP_ACCESS_WRITE : "Ändern", GROUP_ACCESS_STRUCTURE : "Ändern und erweitern", GROUP_ACCESS_OWNER : "Vollzugriff", WIKI_URL : "http://de.wikipedia.org/w/index.php?title=", MAP_VIEW_EXPLANATION : "Notizmappe betrachten", COMMUNITY_EXPLANATION : "Mit Lernpartnern verbinden und Notizzettel freigeben", SEARCH_EXPLANATION : "Notizzettel durchsuchen und Artikel importieren", MEMORIZE_EXPLANATION : "Frage-Tags anhand Karteikasten wiederholen", CLIPBOARD_EXPLANATION : "Entfernte Tags wieder einfügen", INSERT_EXPLANATION : "Links, Bilder und andere Multimedia-Inhalte einfügen", SHARED_BY : "Von", PROBLEM_LIST_HEADING : "Fragen und Antworten", PROBLEM_LIST_HEADERS : ['Frage', 'Stand', 'Freund', 'Gruppe'], ANSWERED : "Beantwortet", UNSOLVED : "Ungelöst", YOU : "Du", BY_YOU : "dir", INVITE_LIST_HEADERS : ['Freund', 'Email'], GROUP_REQUESTED_FROM_TOPIC : "Ein Administrator muss dich noch für die Gruppe freischalten. Sobald dies geschehen ist, kannst du auf die Notizmappe zugreifen.", JOINED_GROUP : "Der Gruppe wurde beigetreten", CARD_LOCKED : "<strong>Notizzettel wird gerade bearbeitet</strong> und kann nicht verändert werden.", STAY_READ_ONLY : "Nur lesen", CHECK_OUT_TRY_AGAIN : "Nochmal versuchen", CARD_WAS_DELETED : "Card has been deleted.", COULD_NOT_GET_TOPIC_REVISION : "Could not load topic revision", CREATE_ADVANCED : "Weiterführende Notizen zum aktuellen Notizzettel machen", SWITCH_ADVANCED : "Weiterführende Notizen zeigen", SWITCH_MAIN : "Grundlegende Notizen zeigen", CREATE_ILLUSTATIVE : "Veranschaulichende Notizen zum aktuellen Notizzettel machen", SWITCH_ILLUSTATIVE : "Veranschaulichende Notizen zeigen", MEMORIZE_ALL : "Gesamte Mappe", MEMORIZE_STUDIED : "In dieser Sitzung bearbeitete", MEMORIZE_CURRENT : "Momentan sichtbare", FIND_GROUP : "Gruppe finden", MINIMIZE : "Minimieren", MAXIMIZE : "Maximieren", COULD_NOT_LOAD_GROUPS : "Konnte Gruppen nicht laden", REFER_FLOAT_CONTEXT : "(Auswahl wird im Kontext-Panel angezeigt)", BLOCK_MODE : "Blockmodus", RESET_SIZE : "Größe zurücksetzen", HELP : "Hilfe...", LATEX_HELP_LINK : "http://www.mediabird.net/de/about/latex", UNKNOWN : "Unknown", PROBLEM_SOLVED : 'Problem gelöst!', AUTHOR : 'Autor', INVITATION : "Einladung", REQUEST_BY : "Anfrage von", INSERT_TABLE : "Tabelle", EXTERNAL_REFERENCE : "Externer Verweis", CLICK_TO_FOLLOW : 'Gehe zu Verweis', REFERENCES : "Verweise", RELATIONS_LIST_HEADERS : ['Ziel', 'Aktion'], ADD_REFERENCE : 'Verweis hinzufügen', EDIT_REFERENCE : 'Verweis bearbeiten', QUICK_REFERENCE : "Direktverweis einfügen", LINK_CURRENT : "Auf aktuelle Seite", LINK_CURRENT_EXPLANATION : "Fügt eine Referenz auf die Seite außerhalb des Notizblocks ein", LINK_CURRENT_NOTE : "Auf Notizen im Panel", LINK_CURRENT_NOTE_EXPLANATION : "Fügt eine Referenz auf den rechts angezeigten Notizzettel ein", LINK_CURRENT_WIKI : "Auf Wikipedia-Artikel", LINK_CURRENT_WIKI_EXPLANATION : "Fügt eine Referenz auf den rechts angezeigten Wikipedia-Artikel ein", REF_EXISTS : "Verweis mit selbem Ziel existiert bereits", ERROR_REF_EMPTY : "Ziel oder Titel fehlt", LOGOUT : 'Abmelden', OPEN_SHEET : "Öffne Zettel ", ALERT_HEADER : "Info", GIVE_US_FEEDBACK : "Gib uns Feedback", SEND_FEEDBACK : "Feedback geben", SEND : "Senden", MESSAGE_EMPTY : "Deine Nachricht ist leer", THANKS_FEEDBACK : "<h3>Danke für dein Feedback.</h3><p>Wir kümmern uns drum.</p>", FEEDBACK_ERROR : "Konnte Feedback nicht senden", FOLLOW_LINK : "Link öffnen", EDIT_LINK : "Bearbeiten", INSERT_COLUMN : "Spalte einfügen", INSERT_ROW : "Zeile einfügen", DELETE_COLUMN : "Spalte löschen", DELETE_ROW : "Zeile löschen", TOPIC_COULD_NOT_BE_UPDATED : "Die Notizmappe konnte nicht aktualisiert werden. Du kannst keine Notizzettel entfernen, die gerade editiert werden.", DELETE_TOPICS : "Angehakte Notizmappen löschen", DELETE_TOPICS_CONFIRM : "Folgende Notizmappen werden unwiderruflich gelöscht:\n####.\n\nBist du sicher, dass du fortfahren möchtest?", COULD_NOT_DELETE_TOPICSS : "Konnte die ausgewählten Notizmappen nicht löschen.", MANAGE_TOPICS : "Alle Notizmappen...", SAVE : "Speichern", SAVE_CLOSE : 'Speichern und schließen', SWITCH_TO_FULL : "Notizzettel mit erweiterter Funktionalität bearbeiten", TOPICS_SHARED_WITH_GROUP : "In der Gruppe freigegebene Notizmappen", GROUP_TOPIC_LIST_HEADERS : ["Notizmappen", "Ersteller", "Zugriff"], MAX_NO_OF_CARDS_IN_TOPIC : "Notizmappe voll. Die Notizmappe enthält die maximale mögliche Anzahl von Notizzetteln. Lege eine neue Mappe an oder ordne die Notizen anders an.", NO_GROUP_CHANGES : "Keine Änderungen seit letzter Speicherung.", CHECK_TOPICS_TO_DELETE : "Setze ein Häkchen vor die Notizmappen, die du löschen möchtest", HISTORY : "Vorherige Notizmappen", GO_BACK_TO : "Zurückgehen zu", NAVI_RIGHT_ADD_EXPLANATION : "Leeren Notizzettel einfügen und öffnen", NAVI_RIGHT_EXPLANATION : "Nächsten Notizzettel anzeigen", NAVI_LEFT_EXPLANATION : "Zurück zum vorherigen Notizzettel", REPETITION : "Wiederholung", OPTIONAL : "optional", SHARE : "Freigeben", YOUR_QUESTION : "Deine Frage"}
                  client.lang.es = {
                     code : 'es', _H1 : 'Encabezado 1', _H2 : 'Encabezado 2', _H3 : 'Encabezado 3', ADD_LEFT : 'Insertar ficha a la izquierda', ADD_REFERENCE : 'Añadir referencia', ADD_RIGHT : 'Insertar ficha a la derecha', ADD_TRANSLATION : 'Añadir', ANNOTATION : 'Anotación', ANNOTATE : 'Anotar', ANSWER : 'Respuesta', CORRECT_TRANSLATION : 'Traducción correcta', ANSWER_MODE_BY_RANGE : 'Usar selección', ANSWER_MODE_EXPLICIT : 'Definir explícitamente', ANSWER_MODE : 'Modo respuesta', ANSWER_RIGHT : 'Lo sabía', ANSWER_TOO_LONG : 'Respuesta demasiado larga.', ANSWER_UNSURE : 'Repetir de nuevo más tarde', ANSWER_WRONG : 'Estaba equivocado', BOLD : 'Negrita', BOX_LEVEL_BRIDGE : ' en el nivel de la caja ', CANCEL : 'Cancelar', CAPTCHA : 'Código de seguridad', CAPTCHA_MISSING : 'Código de seguridad perdido', CARD_EMPTY : '(Está hoja no está vacía)', NOTE_SHEET : 'Hoja de apuntes', CATEGORY : 'Palabras claves', CLICK_TO_ENTER : '(Pulse aquí para empezar a tomar notas)', CLICK_TO_ENTER_ADVANCED : '(Pulse aquí para tomar notas avanzadas)', CLICK_TO_ENTER_ILLUSTRATIVE : '(Pulse aquí para ilustrar las notas que usted ha tomado)', ILLUSTRATION_OF : 'Ilustración de ', MORE_DETAILS_ON : 'Más detalles', CLOSE : 'Cerrar', CONFIRM_CLOSE : 'Si lo confirma, se perderán todos los cambios no guardados.', CONFIRM_REQUEST_SENT : 'Se ha enviado un correo de confirmación a su dirección de correo.', ACCOUNT_CREATED : 'La cuenta ha sido creada, use sus credenciales para entrar.', CONNECTION_DOWN : 'No se ha podido mantener la conexión. Intente recargar la página.', CONNECTION_ERROR : 'La conexión se ha perdido. Intente recargar la página.', COPY : 'Copiar selección', COPY_SECURITY : 'La orden copia no está disponible debido a las restricciones de su navegador. Por favor use en su lugar el atajo Ctrl+C.', COULD_NOT_UPDATE_TOPIC : 'No se pudo actualizar la carpeta de apuntes', COULDNT_COPY_IMAGE : 'No se pudo copiar la imagen en su carpeta destino', CREATE : 'Crear', CUT : 'Cortar seleccion', CUT_SECURITY : 'La orden cortar no esta disponible debido a las restricciones de su navegador. Utilice en su lugar el comando Ctrl+X.', DEFINITION : 'Definición', DEFINE_SELECTION : 'Definir', DEFINITION_QUESTION : 'Cuál es la definición de: ', DELETE : 'Borrar', DESCRIBE_ISSUE : '(Pulse aquí para describir el tema)', DESCRIPTION : 'Describa la nueva característica', MEDIABIRD_DESCRIPTION : '<p>"Apuntes" (Mediabird) es un software especialmente útil para el aprendizaje cooperativo. Los usuarios pueden tomar notas en las materias que ellos deseen y debatir interrogantes en el contexto en el que aparezcan.</p><p>Introduzca sus credenciales y pulse para entrar o registrar un nueva cuenta usando "Registrar nueva cuenta".</p>', BROWSER_BAD_TEXT : '<h3>Su navedador o la versión del mismo no están soportados. Actualice su navegador o instale el navegador recomendado <a href="http://www.getfirefox.com" target="_blank">Firefox</a></h3>', EDIT_CARD_FROM_MAP : 'Editar el contenido de esta nota', EDIT_EMPTY_CARD_FROM_MAP : 'Crear contenido para esta nota', SAVE : 'Guardar', SAVE_CLOSE : 'Guardar y cerrar', CANNOT_DELETE_WHILE_OPEN : 'No puedo borrar la actual hoja de apuntes. Abra una nota diferente e inténtelo de nuevo.', CANNOT_DELETE_LAST : 'No se puede abrir la última hoja abierta. Pulse sobre "Organizar carpetas de apuntes" y borre la carpeta de apuntes.', TRAINER_HEADING : 'Memorizar carpeta de apuntes', EDIT : 'Editar', EDIT_MAP : '<h1>Reorganice las hojas de apuntes con el ratón.</h1><p>Arrástrelas <strong>arriba y abajo</strong> para modificar su nivel.</p>', EXTEND_MAP : '<h1>Cree nuevas notas o navegue por las existentes.</h1><p>Arrastre nuevos ítem <strong>arriba o abajo</strong> para modificar su nivel.</p>', EDIT_MAP_ADD : '<h1>Ha llegado a su última hoja de apuntes.</h1><p>Usted puede <strong>añadir más</strong> usando el botón \'+\'-.</p>', EDIT_MAP_ADD_LEFT : '<h1>Está usted en la primera hoja de apuntes.</h1><p>Puede <strong>añadir más</strong> usando el botón \'+\'-.</p>', SHOW_MAP : '<h1>Revisar la carpeta.</h1>', EDITOR_TITLE : 'Editor', EMAIL : 'Correo electrónico', EMAIL_MISSING : 'Correo perdido', INVALID_EMAIL : 'Ha introducido una dirección de correo errónea', EMPTY_ROW : '(No hay ningún ítem en la lista)', ENTER_ANNOTATION : '(Pulse aquí para escribir una anotación)', ENTER_ANSWER : '(Pulse aquí para responder)', ENTER_DEFINITION : '(Pulse aquí para escribir la definición de la expresión solicitada)', SUGGEST_ANSWER : '(Pulse aquí si quiere sugerir una respuesta a la pregunta)', ENTER_EXPRESSION : '(Pulse aquí e introduzca la expresión definida por la selección)', ENTER_NEW_CARD_TITLE : 'Introducir un nuevo título para la hoja de apuntes', ENTER_QUESTION : '(Pulse aquí para introducir la pregunta)', ERROR_CHANGING_STATUS : 'No se pudieron actualizar los derechos de acceso para este grupo. Intente actualizarlos de nuevo.', ERROR_CREATING_USER : 'Ocurrió un error en la creación de un nuevo usuario.', ERROR_FUNCTION_DISABLED : 'La función fue deshabilitada por el administrador.', ERROR_LOADING_TRAINING : 'La última operación de carga no fue correcta', ERROR_MOVE : 'Ha ocurrido un error al subir el archivo', ERROR_TYPE : 'El tipo de archivo no es correcto. Suba sólo imágenes, por favor.', ERROR_NO_FILE : 'No ha sido subido ningún archivo.', ERROR_QUOTA : 'Ha alcanzado el límite de cuota de su cuenta.', ERROR_SENDING_CONFIRM : 'Hubo un error al enviar el email de confirmación.', ERROR_TOO_BIG : 'La imagen que ha seleccionado es demasiado grande.', ERROR_WHILE_UPLOADING : 'La imagen no pudo subirse.', FILE : 'Archivo', FLASH_CARD_BOXES : 'Cajas de fichas', FIND_HOW_TO : 'Utilice CTRL+F para buscar en la actual hoja de apuntes.', FIND : 'Buscar...', FLOAT_LEFT : 'Colocar texto a la derecha', FLOAT_NONE : 'Sin plataforma', FLOAT_RIGHT : 'Colocar texto a la izquierda', FLOAT_STYLE : 'Estilo flotante', FORMAT : 'Formato', FORMULA_CONTAINS_BLACKLISTED : 'El código LaTeX contiene etiquetas prohibidas.', FORMULA_TOO_BIG : 'Fórmula demasiado grande como para poder mostrarse', FORMULA_TOO_LONG : 'Ecuación demasiada larga', GENERAL_PREFIX : 'Piense en la respuesta a la siguiente ficha de la hoja de apuntes', GET_NEW_CAPTCHA : 'Obtener un nuevo código', GET_PASSWORD : 'Contraseña olvidada', GHOST_ENTRY : '(pulse aquí para añadir un nuevo ítem)', GHOST_TOPIC : '(pulse aquí para añadir una nueva carpeta)', GO : 'Ir', GOTO_ANSWER : 'Respuesta corta', GOTO_QUESTION : 'Mostrar pregunta de nuevo', SHOW_CONTEXT : 'Mostrar contexto', CONTEXT : 'Contexto', GROUP : 'Grupo', HEIGHT : 'Altura', HIGHLIGHT : 'Resaltar', HTML_CODE : 'Código HTML', CODE : 'Código', IMP_LEVEL : 'Marcado como importante', IMP_LEVEL_BY : 'Marcado como importante por', MARK_IMPORTANT : 'Marcar como importante', IN : 'en', INDENT : 'Aumentar sangrado', INSERT_HTML : 'HTML', INSERT_CODE : 'Código', INSERT_IMAGE : 'Imagen', INSERT : 'Insertar', INSERT_LATEX : 'Ecuación', AUTO_CONVERT_LATEX : 'Automático', AUTO_CONVERT_EXPLANATION : 'Convertir automáticamente el código LaTeX en ecuaciones desde la actual hoja de apuntes.', AUTO_LATEX_NO_JOBS : 'No se encontró el código LaTeX en la nota', LATEX_SOURCE : 'Ecuación', INSERT_LINK : 'Enlace', INSERTORDEREDLIST : 'Numeración', INSERTUNORDEREDLIST : 'Viñetas', INVALID_END : '" no es válido', INVALID_START : 'El valor de "', IS_IN : 'está escrito en', ISSUE : 'Problema', ASK_QUESTION : 'Hacer pregunta', ANSWER_LINK : 'Insertar pregunta', ITALIC : 'Cursiva', INVITE : 'Invitar', NO_INVITEES_SELECTED : 'No especificó a quién quiere invitar al grupo', INVITE_USER : 'Invitar a un amigo', INVITE_USERS_TO_GROUP : 'Invitar a un amigo al grupo', FOLLOWING_NOT_FOUND : 'Los siguientes amigos no pueden ser invitados si no han sido registrados en Apuntes:', NONE_INVITED : 'Sus amigos ya han sido invitados.', COULD_NOT_INVITE_USERS : 'No se puede invitar a amigos', EMAIL_OR_NAME : 'Direcciones de correo', NO_USERS_TO_INVITE : '(No hay amigos conocidos para invitar)', FURTHER_USERS_INVITE : 'Por direcciones de correo', EMAIL_SEPARATE_EXPLANATION : 'Separar cuentas de correo con comas.', KNOWN_USERS : 'Amigos', JUST_ENABLED : 'Su cuenta ha sido correctamente activada. Utilice el formulario inferior para entrar.', JUSTIFY_CENTER : 'Centrar', JUSTIFY_FULL : 'Justificado completo', JUSTIFY_LEFT : 'Alinear a la izquierda', JUSTIFY_RIGHT : 'Alinear a la derecha', LANGUAGES : ['Inglés', 'Afar', 'Abkhazian', 'Afrikaans', 'Akan', 'Albano', 'Amharic', 'Árabe', 'Aragonés', 'Armenio', 'Assamese', 'Avaric', 'Avestan', 'Aymara', 'Azerbaijani', 'Bambara', 'Bashkir', 'Euskera', 'Bieloruso', 'Bengalí', 'Bihari', 'Bislama', 'Bosnio', 'Breton', 'Búlgaro', 'Burmese', 'Catalán', 'Chamorro', 'Chechen', 'Chichewa', 'Chinese', 'Church Slavic', 'Chuvash', 'Cornish', 'Corsican', 'Cree', 'Croata', 'Checo', 'Danés', 'Divehi', 'Dutch', 'Dzongkha', 'Esperanto', 'Estonio', 'Ewe', 'Faroese', 'Fijian', 'Finlandés', 'Francés', 'Fulah', 'Gallego', 'Ganda', 'Georgian', 'Alemán', 'Griego', 'Guarani', 'Gujarati', 'Haitian', 'Hausa', 'Hebrew', 'aquíro', 'Hindi', 'Hiri Motu', 'Húngaro', 'Icelandic', 'Ido', 'Igbo', 'Indionesio', 'Interlingue', 'Inuktitut', 'Inupiaq', 'Irlandés', 'Italiano', 'Japonés', 'Javanese', 'Kalaallisut', 'Kannada', 'Kanuri', 'Kashmiri', 'Kazakh', 'Khmer', 'Kikuyu', 'Kinyarwanda', 'Kirghiz', 'Kirundi', 'Komi', 'Kongo', 'Korean', 'Kurdish', 'Kwanyama', 'Lao', 'Latin', 'Latvian', 'Limburgish', 'Lingala', 'Lithuanian', 'Luba-Katanga', 'Luxemburgés', 'Macedonio', 'Malagasy', 'Malay', 'Malayo', 'Maltese', 'Manx', 'Maori', 'Marathi', 'Marshallese', 'Moldavian', 'Mongolian', 'Nauru', 'Navajo', 'Ndonga', 'Nepali', 'North Ndebele', 'Northern Sami', 'Noruego', 'Noruego Bokmal', 'Noruego Nynorsk', 'Occitan', 'Ojibwa', 'Oriya', 'Oromo', 'Ossetian', 'Pāli', 'Panjabi', 'Pashto', 'Persian', 'Polish', 'Portugués', 'Quechua', 'Raeto-Romance', 'Rumano', 'Ruso', 'Samoan', 'Sango', 'Sanskrito', 'Sardinian', 'Scottish Gaelic', 'Serbio', 'Serbo-Croata', 'Shona', 'Sichuan Yi', 'Sindhi', 'Sinhala', 'Eslovaco', 'Eslovaco', 'Somali', 'Sotho', 'South Ndebele', 'Español', 'Sundanés', 'Swahili', 'Swati', 'Sueco', 'Tagalog', 'Tahitian', 'Tajik', 'Tamil', 'Tatar', 'Telugu', 'Thai', 'Tibetan', 'Tigrinya', 'Tonga', 'Tsonga', 'Tswana', 'Turco', 'Turkmen', 'Twi', 'Uighur', 'Ukrainian', 'Urdu', 'Uzbek', 'Venda', 'Vietnamita', 'Volapuk', 'Walloon', 'Welsh', 'Western Frisian', 'Wolof', 'Xhosa', 'Yiddish', 'Yoruba', 'Zhuang', 'Zulu', 'Latín', 'Griego clásico', 'Fusha'], LEVEL : 'Nivel', LEVEL_IMPORTANT : 'Importante', LEVEL_ADVANCED : 'Avanzado', LEVEL_ILLUSTRATIVE : 'Ilustrativo', LINK_HEADING : 'Editor de enlaces', LINK_TYPE : 'Tipo de enlace', LINK_REQUIRES_SELECTION : 'Insertar un enlace requiere antes hacer la selección a la que será enlazada', COLLAPSIBLE_REQUIRES_SELECTION : 'Insertar una nota desplegable requiere una selección que debe ser desplegable', LOCATION : 'Localización', LOGON : 'Login', COULD_NOT_CLOSE_SESSION : 'No se pudo cerrar la sesión. Recargue la página para comenzar una nueva sesión.', SET_ACCESS_NONE : 'Sin acceso', SET_ACCESS_READ : 'Acceso de sólo lectura', SET_ACCESS_WRITE : 'Acceso de escritura', SET_ACCESS_STRUCTURE : 'Acceso completo', TOO_MANY_STEPS : 'Ha alcanzado el número máximo de hojas de apuntes en esta carpeta.', MOVE_LEFT : 'Mover a la izquierda', MOVE_RIGHT : 'Mover a la derecha', NAME : 'Nombre', NAME_MISSING : 'Nombre de amigos desaparecido', NO_ANSWER : 'No introdujo una respuesta.', NO_ANNOTATION : 'No introdujo una anotación.', NO_DEFINITION : 'No escribió ninguna definición.', NO_QUESTION : 'No introdujo ninguna pregunta.', NO_EXPRESSION : 'No introdujo una expresión.', NO_CARD_TITLE : '(ninguna seleccionada)', NO_FLASH_CARDS : 'La hoja actual no contiene ninguna ficha de vocabulario.', NO_HIGHLIGHT_IN_EMPTY_CARD : 'La función de resalte no está disponible para las hojas de apuntes vacías', NO_URL_GIVEN : 'Para insertar una imagen desde su URL, debe especificar su dirección.', NO_MARKER_FOR_DEL : 'Seleccione primero la etiqueta que desea borrar', NOCAT : '(Sin palabras clave)', NOT_CONFIRMED : 'Por favor, confirme su dirección de correo usando el enlace de confirmación enviado a su dirección de email.', OK : 'OK', OTHER_ERROR : 'Error mientras accedía al script.', OUTDENT : 'Disminuir sangrado', PADDING_BOTTOM : 'Abajo', PADDING_LEFT : 'Izquierda', PADDING_RIGHT : 'Derecha', PADDING_STYLE : 'Estilo de relleno', PADDING_TOP : 'Arriba', PARA : 'Párrafo', PASS_MISSING : 'Contraseña perdida', PASS_NOT_MATCHING : 'Error de contraseña', PASSWORD_ACCESS_KEY : 'P', PASSWORD_ERROR : 'Ocurrió un error mientras se comprobaba la contraseña.', PASSWORD_INVALID : 'Hubo un error al enviar su contraseña a una dirección de correo específica.', PASSWORD : 'Contraseña', PASSWORD_AGAIN : 'Repetir contraseña', PASSWORD_NO_USER : 'La dirección de correo que ha usado no se corresponde con la de ninguna cuenta existente.', PASSWORD_SENT : 'Se ha enviado un mensaje con su contraseña a la dirección de correo electrónico asociada a su cuenta.', PASTE : 'Pegar dentro de la selección', PASTE_SECURITY : 'El comando pegar no está disponible debido a las restricciones de su navegador. Use en su lugar Ctrl+V, por favor.', PLEASE_TRANSLATE : 'Traducir a ', QUESTION_EDIT : 'Pulse para editar ', QUESTION : 'Pregunta', QUESTION_MODE : 'Tipo de etiqueta', QUESTION_TOO_LONG : 'La pregunta es demasiada larga.', QUESTION_VIEW : 'Pulse para ver', REFERENCE : 'Referencia', REDO : 'Rehacer', REMOVE_FORMAT : 'Limpiar formato', REMOVE_LINK : 'Quitar enlace', REMOVE : 'Quitar', REMOVE_THIS : 'Quitar etiqueta', REMOVE_TRANSLATION : 'Quitar traducción', RENAME : 'Renombrar', RENDERING_FAILED : 'Cálculo de ecuación sin éxito.', REP_LEVEL : 'Marcar para repetición', REP_MARKER : 'Repetir más tarde', SHARE : 'Compartir', REPEAT : 'Repetir', REPEAT_TITLE : 'Ha remarcado la zona resaltada de repetición.', REVIEW_RIGHT : 'Revisarlo', REVIEW_UNSURE : 'Decidir más tarde', REVIEW_WRONG : 'Repetir de nuevo', SEARCH : 'Buscar', SEL_REQUIRED_HIGHLIGHT : 'Antes de resaltar, seleccione el texto que quiere destacar.', SELECT_CARD_EXPLANATION : 'Pulse en una hoja de apuntes y escoja "Seleccionar hoja de apuntes".', SELECT_CARD : 'Seleccionar hoja de apuntes', SELECTED_TEXT : 'Selección', SET : 'Fijar', SESSION_CREATION_ERROR : 'Hubo un error de creación de sesión.', SESSION_FINISHED : '¡Sesión terminada! No se han dejado fichas en esta sesión.', SESSION_ABORTED : 'La carga de la página fue abortada', SESSION_INVALID : 'Se envió una respuesta errónea. Esto podría ser un error de programación o un fallo de tiempo de sesión. Intente recargar la página', SIGN_UP : 'Registrar una nueva cuenta', STRIKE_THROUGH : ' Tachado', STUDY_CARD_FROM_MAP : 'Mostrar esta hoja de apuntes', STUDY_TOOL_TITLE : 'Visor', TERMS_ACCEPT : 'He leído y comprendido los <a href="terms.php" target="_blank">Términos de uso</a>.', TERMS : 'Términos de uso', TERMS_MISSING : 'Debe aceptar los términos de uso', TITLE : 'Título', MEDIABIRD_TITLE : 'Apuntes aprendizaje Web2.0 (Mediabird Web2.0-Learning)', TOPIC_BRIDGE : ', carpeta de apuntes ', TOPIC : 'Carpeta de apuntes', TOPIC_EMPTY : 'La carpeta de apuntes no contiene ninguna hoja que pueda ser abierta.', TOPICS : 'Lista de carpeta de apuntes', TRANSLATE_INTO : 'Traducir a', TRANSLATION_EDITOR : 'Editor de traducción', TRANSLATION_INTO : 'a', TRANSLATION : 'Traducción', TRANSLATE : 'Traducir', TRANSLATION_MARKER : 'Editar traducción', TRANSLATION_VIEWER : 'Ver traducción', TYPE_CARD : 'Hoja de apuntes', TYPE_EMAIL : 'Correo-e', TYPE_URL : 'URL', UNDERLINE : 'Subrayado', UNDO : 'Deshacer', UNTITLED : 'Sin título', UPDATE : 'Actualizar', UPLOAD_BUTTON : 'Subir', PICTURE : 'Imagen', URL_INVALID : 'La dirección URL que ha especificado es incorrecta', USER_EMAIL_TAKEN : 'La cuenta de correo que ha introducido ya está en uso en otra cuenta.', USER_NAME_TAKEN : 'El nombre de usuario que ha elegido ya existe para otra cuenta.', WIDTH : 'Anchura', WRONG_PASSWORD : 'La combinación de usuario y contraseña es errónea.', WRONG_SECURITY : 'Código de seguridad introducido incorrectamente', INSERT_COLLAPSIBLE_NOTE_TEXT : '(Escribir texto)', INSERT_COLLAPSIBLE : 'Desplegable', ADD_PREREQUISITE : 'Añadir prerrequisito', REMOVE_PREREQUISITE : 'Remover prerrequisitos', PREREQUISITES : 'Prerrequisitos', NO_TITLE_GIVEN : 'No ha dado ningún título a este prerrequisito', NO_TOPIC_SELECTED : 'No seleccionó una carpeta de apuntes como prerrequisito', PREREQUISITE : 'Prerrequisito para esta carpeta', EDIT_FIRST_CARD_OF_PREREQUISITE : 'Editar primera nota de la hoja de prerrequisito', STUDY_PREREQUISITE : 'Estudiar prerrequisito', IMAGE_ADJUST_HEADING : 'Ajustar las propiedades de imágenes', IMAGE_UPLOAD_HEADING : 'Subir nueva imagen', IMAGE_LOCATION_HEADING : 'Usar localización dada', COULD_NOT_LOAD_TOPICS : 'No se pudo recuperar la lista de carpetas de apuntes', COULD_NOT_LOAD_GROUPS : 'No se pudo obtener la lista de grupo', CREATE_GROUP : 'Crear grupo', VIEW_MY_GROUPS : 'Ver mis grupos', REQUEST_LIST_HEADER : 'Invitaciones de grupo y peticiones de miembros', JOIN_GROUP : 'Unirse al grupo', REQUEST_GROUP : 'Solicitar ser miembro', LEAVE_GROUP : 'Abandonar grupo', ACCEPT_MEMBER : 'Aceptar', REJECT_MEMBER : 'Denegar', PENDING : 'Pendiente', INVITED : 'Invitado', REQUESTED : 'Solicitado', MEMBER_LIST_HEADERS : ['Nombre', 'Nivel'], REQUESTS_LIST_HEADERS : ['Grupo', 'Información', 'Acciones'], SET_TO : 'Administrar', ADMIN_LEVEL : 'Administrador', MEMBER_LEVEL : 'Miembro', EDIT_GROUP_HEADER : 'Editar grupo', VIEW_GROUP_HEADER : 'Ver grupo', GROUP_DESCRIPTION : 'Descripción', GROUP_NAME : 'Nombre', GROUP_CATEGORY : 'Palabras claves', GROUP_ACCESS : 'Acceso para invitados', GROUP_NO_ACCESS : 'Ocultar', GROUP_VIEW_ACCESS : 'Mostrar, permitir solicitud', GROUP_JOIN_ACCESS : 'Mostrar, permitir unirse', COULD_NOT_UPDATE_GROUP : 'No se pudo actualizar el grupo', MEMBER_LIST : 'Miembros', COULD_NOT_SET_MEMBER_ACCESS : 'No se pudo cambiar el nivel de acceso', COULD_NOT_LEAVE_GROUP : 'No se pudo abandonar grupo', COULD_NOT_JOIN_GROUP : 'No se pudo unir al grupo', COULD_NOT_DELETE_GROUP : 'No se pudo borrar el grupo', CANNOT_REMOVE_NONEMPTY_GROUP : 'No se puede borrar un grupo que todavía tiene miembros. Borre los miembros primero.', ACCEPT_INVITATION : 'Aceptar', REJECT_INVITATION : 'Ignorar', CANCEL_REQUEST : 'Borrar petición', COULD_NOT_REMOVE_REQUEST : 'No se pudo borrar petición', COULD_NOT_ACCEPT_REQUEST : 'No se pudo aceptar la petición de ser miembro', COULD_NOT_REJECT_REQUEST : 'No se pudo rechazar la petición de ser miembro', COULD_NOT_REMOVE_MEMBER : 'No se pudo eliminar membresía', CANNOT_LEAVE_GROUP_FROM_EDITOR : 'Usar el administrador de grupos para abandonar grupos a los que se ha unido.', REMOVE_MEMBER : 'Quitarse del grupo', TOPIC_LIST_RECENT_HEADING : 'Historia', GROUPLIST_HEADINGS : ['Grupo', 'Acceso'], GROUPLIST_HEADER : 'Compartidos', CLICK_TO_CHANGE_ACCESS : 'Pulse para cambiar el nivel de acceso de los miembros del grupo', ADD_GROUP : 'Compartir carpeta de apuntes', SHARE_FOLDER : 'Organizar compartidos', SHARE_FOLDER_EXPLANATION : 'Organizar compartidos en la carpeta actual de apuntes', SHARE_TOPIC_WITH_GROUP : 'Nuevo elemento compartido en la carpeta', FILE_AS : "Publicado como", MEMBER_RIGHTS : 'Derechos de miembros', RIGHT_READONLY_PRESET : 'Ver', RIGHT_WRITE_PRESET : 'Ver y editar', RIGHT_STUCTURE_PRESET : 'Ver, editar y reorganizar', NOT_MEMBER_OF_ANY_GROUP : 'No puede compartir apuntes mientras no sea miembro del grupo. Pulse en "Crear grupo" para crearlo.', COULD_NOT_UPDATE_RIGHT : 'No se pudieron actualizar derechos del servidor', PREREQUISITE_ENTER_TITLE : 'Escriba el título del prerrequisito', PREREQUISITE_CHOOSE_TOPIC : 'O elija una carpeta de apuntes de una carpeta de apuntes requerida', NONE : 'Sin acceso', ALREADY_SHARED_WITH_ALL_GROUPS : 'Ya ha compartido estas notas con todos los miembros del grupo a los que pertenece.', LICENSE_NO_RESERVED : 'Dominio Público, sin derechos reservados', LICENSE_BY : 'Attribución (por)', LICENSE_BY_SA : 'Attribution Share Alike (by-sa)', LICENSE_BY_ND : 'Attribution No Derivatives (by-nd)', LICENSE_BY_NC : 'Attribution Non-commercial (by-nc)', LICENSE_BY_NC_SA : 'Attribution Non-commercial Share Alike (by-nc-sa)', LICENSE_BY_NC_ND : 'Attribution Non-commercial No Derivatives (by-nc-nd)', COULD_NOT_CHANGE_TOPIC_LICENSE : 'No se pudo cambiar la licencia de la carpeta de apuntes', LICENSE_TEXT : 'Licencia', LICENSE_EXPLANATION : 'Explicación detallada...', LICENSE_EXPLANATION_LINK : 'http://creativecommons.org/about/license/', LICENSE_HEADER : 'Contenido de la Licencia', LICENSE_APPLICATION : 'La licencia se aplica tan pronto como este contenido es compartido con los miembros del grupo.', NO_RECENT_ACTIONS : '(Historial reciente)', NO_CARD_SELECTED : 'No ha seleccionado una hoja de apuntes. Pulse en "Seleccionar hoja de apuntes" para hacerlo así.', BALLOON_TEXTS : {
                        DESKTOP : {
                           CLIPBOARD_EXPLANATION : '<h3>Cajón de etiquetas. </h3><p>Aquí es donde son eliminadas las etiquetas. Algunas hojas en el fichero indican que se han eliminado etiquetas.</p>', HIGHLIGHTER_EXPLANATION : '<p>Ahora, <strong>resalte algún texto</strong> en la hoja de apuntes</p>', NO_TAGS_EXPLANATION : '<h3>Fichero</h3><p>No hay <strong> fichas de vocabulario</strong> en el fichero.</p><p>Use el marcador <strong>para resaltar apuntes</strong> e insertar preguntas.</p>', NO_TAGS_aquí_EXPLANATION : '<h3>Memorizar carpeta de apuntes</h3><p>no hay aquí<strong>fichas de vocabulario</strong>.</p><p>Use el rotulador, para <strong>resaltar notas</strong> e insertar preguntas.</p><p><a título="Abrir el fichero para memorizar la hoja de apuntes" class="mem" href="javascript:void(0)">Memorizar toda la carpeta de apuntes</a>.</p>', CHOOSE_SCOPE : '<h3>Memorizar apuntes</h3><p>Elegir el <strong>rango de apuntes</strong> que quiere memorizar.</p><div class="options"></div>'}
                        , EDITOR : {
                           TOO_MUCH_CONTENT : '<h3>El contenido excede el tamaño de la hoja</h3><p>para hacer esta hoja de apuntes más fácil de obtener, <strong>guardar abreviada</strong>.</p><p class="add-hint">Insertar <strong>hojas de apuntes futuras</strong> para contenido adicional usando el botón flip de la página.</p>'}
                        , TRAINER : {
                           ANSWER_UP : '<h3>Tarjeta de vocabulario subida</h3><p>Una vez que ha respondido la pregunta <strong>correctamente</strong>, la tarjeta ha sido movida <strong>al nivel superior</strong>.</p>', ANSWER_REVERT : '<h3>Tarjeta de vocabulario movida atrás</h3><p>Puesto que ha marcado su respuesta como <strong>incorrecta</strong>, la ficha ha sido movida <strong>atrás hacia el primer nivel</strong>.</p>', ANSWER_DOWN : '<h3>Tarjeta de vocabulario movida hacia abajo</h3><p>Puesto que <strong>no sabía la respuesta</strong> a la pregunta, la tarjeta de vocabulario ha sido movida <strong>al nivel inferior</strong>.</p>', ANSWER_REMOVED : '<h3>Ficha eliminada</h3><p>Dado que ha respondido a esta pregunta correctamente <strong>cuatro veces en esta tanda</strong>, la ficha ha sido eliminada <strong>de esta sesión de entrenamiento</strong>.</p>'}
                        }
                     , IMAGE_EDITOR : "Editor de imágenes", REVERT_CHANGES : "Revertir todos los cambios de esta hoja de apuntes", REVERT_CHANGES_TEXT : "Todos los cambios de esta hoja de apuntes y sus etiquetas se perderán. ¿Está seguro de querer continuar?", EMPTY_TRASH : "Portapapeles de etiqueta vacía", MOVE_TO_TRASH : "Mover al portapapeles de etiqueta", INSERT_FROM_TRASH : "Insertar desde el portapapeles de etiquetas", CARD_UPDATE_PENDING : "Sus notas están siendo guardadas. Si aborta el proceso de guardado, todos los cambios se perderán.", CARD_UPDATE_FAILURE : "La subida de sus notas sobre #### falló. ¿Le gustaría intentarlo de nuevo?", ABORT_UPLOAD : "Abortar actualización y revertir cambios", DISMISS_DATA : "Revertir cambios", RETRY_UPLOAD : "Reintentar guardar", CARD_WAS_DELETED : "La tarjeta fue borrada.", COULD_NOT_GET_TOPIC_REVISION : "No se pudo obtener tema de revisión", RESET_FONT : "Resetear tipografía", NOTIFICATION_MESSAGES : ["Un amigo suyo tiene una dificultad. Pulse aquí para ayudarle."], NOTIFICATION_TITLES : ["Nuevo Problema"], NEEDS_SHARING_TO_GET_SOLVED_HTML : "<strong>Esta etiqueta no es visible por otros usuarios</strong> ya que la carpeta de apuntes no está compartida con otros usuarios. Pulse sobre el botón adecuado para compartirla.", PASTE_CURRENT_SELECTION : "Pegar selección de la hoja de apuntes", NOTIFICATIONS : "Noticias de mis grupos", OPEN_INVITATIONS_HTML : "Tiene <span/>&nbsp;invitaciones de grupo.", OPEN_REQUESTS_HTML : "Ha solicitado <span/>&nbsp;ser miembro.", OPEN_OTHERS_REQUESTS_HTML : "Hay <span/>&nbsp;peticiones de miembros.", QUESTION_REQUESTS_HTML : "Hay <span/>&nbsp;problemas sin resolver.", EMPTY_PROBLEM : "(No hay preguntas disponibles)", FIND_FRIENDS_HEADER : "Encontrar compañeros", FIND_FRIENDS_MORE : "más...", SHARING_SUB_HEADER : "Compartir", SHARING_MORE : "más...", SHARED_WITH : "Carpeta de apuntes compartida", UNSHARED : "Todavía no se ha compartido esta carpeta de apuntes", SHARED_WITH_YOU : "No hay ninguna carpeta compatida con usted", EDIT_SHARING_HEADER : "Compartir y dar permisos a carpeta de apuntes actual", VIEW_SHARING_HEADER : "Ver información compartida y permisos", GROUP_FINDER_HEADER : "Buscar en grupos", FIND_BUDDIES_HTML : "<p><strong>Usar el campo de búsqueda</strong> para encontrar compañeros de estudios, amigos o grupos.</p>", NO_GROUP_RESULTS_HTML : "<p><strong>No se encontraros grupos.</strong> Intente una búsqueda menos específica.</p>", COULD_NOT_FIND_BUDDIES_QUESTION : "¿No pudo encontrar compañeros? ¡Cree un nuevo grupo e invítelos!", NEWS : "Notificaciones", COULD_NOT_RETRIEVE_NOTIFICATION_MESSAGE : "No se pueden obtener notificaciones.", NO_NOTIFICATIONS_HTML : "<p><strong>Sin notificaciones</strong>.</p>", COULD_NOT_HIDE_MESSAGE : "No se pudo marcar el mensaje como leído.", INSERT_PAGE : "Insertar hoja", START_TOPIC : "Nueva carpeta de apuntes", BACK : "Atrás", FLIP_PAGE : "Voltear hoja", CREATE_NEW_TOPIC : "Nueva carpeta de apuntes", CREATE_NEW_TOPIC_EXPLANATION : 'Cerrar carpeta de apuntes actual y crear una nueva', CLEANUP_TOPIC : 'Limpiar carpeta de apuntes', CLEANUP_TOPIC_EXPLANATION : 'Eliminar hoja de apuntes de la carpeta de apuntes', PROGRESS_HEADER : "Carpeta de apuntes actual", NEW_MORE : "nuevo...", NEW_TOPIC_EXPLANATION : "Abrir nueva carpeta de apuntes", ADJUST_MORE : "editar...", ADJUST_EXPLANATION : "Ordenar la actual hoja de apuntes, organizarlas e insertar nuevas", COULD_NOT_CHECK_REVISION : "No se pudo comprobar unanueva versión para esta hoja de apuntes", COULD_NOT_SYNC_TRAINING : "Los datos de prueba no han podido ser sincronizados", TRASH_EMPTY : "El portapapeles de etiquetas está actualmente vacío", NO_CONTENT_FOR_HIGHLIGHT : "El rotulador no puede ser usado en una hoja vacía. Inserte algunos apuntes primero.", NO_CONTENT_FOR_REINSERT : "Las etiquetas eliminadas no pueden ser reinsertadas en una hoja de apuntes vacía.", MANAGE_TRASH : "Gestionar etiquetas del portapapeles", INSERT_TRAINABLE_MARKERS : "Para memorizar esta hoja de apuntes, debería insertar definiciones, preguntas o problemas. Esto permite enlazar con sus preguntas con respuestas en el contexto relacionado con la parte del texto.", DO_MORE_FLASH_CARDS : "Para memorizar adecuadamente los apuntes de la hoja, debería insertar primero etiquetas.", SEARCH_MY_TOPICS_HEADER : "Buscar y organizar carpetas de apuntes", FIND_MY_TOPIC_HTML : "<p><strong>Usar el campo de búsqueda</strong> para buscar carpetas de apuntes disponibles</p>", NO_RESULTS_OF_MY_TOPIC_SEARCH_HTML : "<p><strong>Ninguna de sus carpetas de apuntes se enlaza con su búsqueda.</strong> Inténtelo con una búsqueda menos específica.</p>", SEARCH_ARTICLES_HEADER : "Buscar e importar artículos", FIND_SEARCH_HTML : "<p><strong>Usar el campo de búsqueda</strong> para buscar y ver artículos de la Wikipedia y hojas de apuntes compartidas con otros.</p>", NO_RESULTS_HTML : "<p><strong>Ningún ítem coincide con su petición.</strong> Inténtelo con una consulta más general.</p>", SEARCH_MORE : "más...", DRAG_TO_LEFT_EXPLANATION : "Seleccione algún texto y arrástrelo sobre la hoja de apuntes.", VIEW_RESULT_EXPLANATION : "Buscar resultados mostrados abajo", COULD_NOT_LOAD_CARD : "No se pudo obtener la hoja de apuntes seleccionada", DO_NOT_SHOW_AGAIN : "No mostrar de nuevo", WIKI_AJAX_URL : "http://es.wikipedia.org/w/api.php?action=opensearch&search=", SEARCH_WIKIPEDIA : 'Buscar en Wikipedia', REMOVE_ALERT : "ocultar", GROUP_ACCESS_READONLY : "Sólo lectura", GROUP_ACCESS_WRITE : "Modificar", GROUP_ACCESS_STRUCTURE : "Modificar y extender", GROUP_ACCESS_OWNER : "Acceso completo", WIKI_URL : "http://es.wikipedia.org/w/index.php?title=", MAP_VIEW_EXPLANATION : "Ver carpeta actual de apuntes", COMMUNITY_EXPLANATION : "Conectar con los compañeros y compartir su trabajo con ellos", SEARCH_EXPLANATION : "Buscar su hoja de apuntes e importar artículos", MEMORIZE_EXPLANATION : "Memorizar etiquetas usando la caja de fichas", CLIPBOARD_EXPLANATION : "Reinsertar etiquetas suprimidas", INSERT_EXPLANATION : "Insertar enlaces, imagenes y otro contenido multimedia", PROBLEM_LIST_HEADING : "Preguntas y Respuestas", PROBLEM_LIST_HEADERS : ['Pregunta', 'Estado', 'Colegas', 'Grupo'], ANSWERED : "Respondido", UNSOLVED : "Sin resolver", YOU : "Usted", BY_YOU : "Por usted", INVITE_LIST_HEADERS : ['Colegas', 'Correo'], SHARED_BY : "Por", GROUP_REQUESTED_FROM_TOPIC : "Un administrador ha confirmado su petición. Tan pronto como se una al grupo podrá ver las carpetas compartidas.", JOINED_GROUP : "Grupo fue unido", CARD_LOCKED : "<strong>La hoja de apuntes está siendo editada</strong> por otro usuario en este momento así que no se permiten cambios ahora.", STAY_READ_ONLY : "Continuar como solo lectura", CHECK_OUT_TRY_AGAIN : "Intentar de nuevo", CREATE_ADVANCED : "Tomar notas avanzadas relacionadas con la actual hoja de apuntes", SWITCH_ADVANCED : "Mostrar notas avanzadas", SWITCH_MAIN : "Mostrar notas esenciales", CREATE_ILLUSTATIVE : "Tomar notas ilustrativas relacionadas con la hoja de apuntes actual", SWITCH_ILLUSTATIVE : "Mostrar notas ilustrativas", MEMORIZE_ALL : "Carpeta de apuntes al completo", MEMORIZE_STUDIED : "Hojas estudiadas en esta sesión", MEMORIZE_CURRENT : "Hoja actual", FIND_GROUP : "Encontrar grupos", MINIMIZE : "Minimizar", MAXIMIZE : "Maximizar", COULD_NOT_LOAD_GROUPS : "Los grupos no han podido ser cargados", REFER_FLOAT_CONTEXT : "(Notas seleccionadas mostradas en el panel contexto)", LATEX_EDITOR : "Editor de ecuaciones, LaTeX", BLOCK_MODE : "Modo bloque", RESET_SIZE : "Resetear tamaño", HELP : "Ayuda...", LATEX_HELP_LINK : "http://www.mediabird.net/en/about/latex", UNKNOWN : "Desconocido", PROBLEM_SOLVED : '¡Problema resuelto!', AUTHOR : 'Autor', INVITATION : "Invitación", REQUEST_BY : "Solicitado por", INSERT_TABLE : "Tabla", EXTERNAL_REFERENCE : "Referencia externa", CLICK_TO_FOLLOW : 'Pulse para seguir referencia', REFERENCES : "Referencias", RELATIONS_LIST_HEADERS : ['Localización', 'Acción'], EDIT_REFERENCE : 'Editar referencia', QUICK_REFERENCE : "Insertar referencia rápida", LINK_CURRENT : "A la página actual", LINK_CURRENT_EXPLANATION : "Añadir una referencia a la página mostrada fuera del bloc de notas", LINK_CURRENT_NOTE : "A las notas en el panel", LINK_CURRENT_NOTE_EXPLANATION : "Añadir una referencia a la hoja de apuntes mostrada a la derecha", LINK_CURRENT_WIKI : "Artículo de la Wikipedia", LINK_CURRENT_WIKI_EXPLANATION : "Añadir referencia a un artículo de la Wikipedia mostrado a la derecha", REF_EXISTS : "Referencia con la misma localización ya añadida", ERROR_REF_EMPTY : "Localización o título perdido", LOGOUT : 'Salir', OPEN_SHEET : "Abrir hoja ", ALERT_HEADER : "Info", GIVE_US_FEEDBACK : "Enviar sugerencias a los desarrolladores de Mediabird", SEND_FEEDBACK : "Enviar información", SEND : "Enviar", MESSAGE_EMPTY : "Su mensaje está vacío", THANKS_FEEDBACK : "<h3>Gracias por su información.</h3><p>La gestionaremos tan pronto nos sea posible.</p>", FEEDBACK_ERROR : "No pudimos enviar su información", FOLLOW_LINK : "Siguiente enlace", EDIT_LINK : "Editar enlace", INSERT_COLUMN : "Insertar columna", INSERT_ROW : "Insertar fila", DELETE_COLUMN : "Borrar columna", DELETE_ROW : "Borrar fila", TOPIC_COULD_NOT_BE_UPDATED : "La hoja de apuntes no puede ser actualizada. No puede borrar una nota que ha sido editada por alguien más.", DELETE_TOPICS : "Borrar las carpetas de apuntes marcadas", DELETE_TOPICS_CONFIRM : "Las siguientes carpetas con notas serán borradas:\n####\n\¿Está seguro de que quiere continuar?", COULD_NOT_DELETE_TOPICSS : "No se pudo borrar la carpeta de apuntes señalada.", MANAGE_TOPICS : "Organizar carpeta de apuntes", SWITCH_TO_FULL : "Ver hoja de apuntes con las características avanzadas", TOPICS_SHARED_WITH_GROUP : "Carpetas de apuntes compartida en grupo", GROUP_TOPIC_LIST_HEADERS : ["Carpeta de apuntes", "Creador", "Acceso"], MAX_NO_OF_CARDS_IN_TOPIC : "Carpeta llena. La carpeta contiene el máximo de hojas de apuntes posible. Cree una nueva carpeta o bien organice sus notas de manera diferente.", NO_GROUP_CHANGES : "Sin cambios desde la última copia guardada.", CHECK_TOPICS_TO_DELETE : "En la lista inferior, señale la carpeta de apuntes que desea borrar", HISTORY : "Carpetas de apuntes recientes", GO_BACK_TO : "Volver a", NAVI_RIGHT_ADD_EXPLANATION : "Añadir una hoja de apuntes vacía y verla", NAVI_RIGHT_EXPLANATION : "Ver siguiente hoja de apuntes", NAVI_LEFT_EXPLANATION : "Ver hoja de apuntes anterior", PDF_INSERT_EXPLANATION : "Importar archivos PDF", PROBLEM_LIST_EXPLANATION : "Mostrar una lista de preguntas sin responder", REPETITION : "Repetición", OPTIONAL : "optional", SHARE : "Compartir", YOUR_QUESTION : "Su pregunta"}
                  client.data.TopicAccessConstants = {
                     noAccess : 0, allowViewingCards : 1, allowSearchingCards : 2, allowCopyingCards : 4, allowEditingContent : 8, allowAlteringMarkers : 16, allowAddingCards : 32, allowRearrangingCards : 64, allowRemovingCards : 128, allowRename : 256, presetReadOnly : 7, presetWriteAccess : 63, presetFullAccess : 511, owner : 1023};
                  client.data.Topic = function() {
                     this.id = null;
                     this.title = lang.UNTITLED;
                     this.category = lang.NOCAT;
                     this.author = null;
                     this.language = null;
                     this.access = client.data.TopicAccessConstants.owner;
                     this.revision = 0;
                     var rights = [];
                     this.addRight = function(right) {
                        if(rights.indexOf(right) ==- 1) {
                           right.topic = this;
                           rights.push(right);
                           }
                        }
                     this.removeRight = function(right) {
                        rights.remove(right);
                        right.topic = null;
                        }
                     this.getRights = function() {
                        return rights.clone();
                        }
                     this.isShared = function() {
                        var shared = false;
                        rights.each(function() {
                           if(this.access != client.data.TopicAccessConstants.noAccess) {
                              shared = true; return false; }
                           }
                        );
                        return shared;
                        }
                     this.getRightCount = function() {
                        return rights.length;
                        }
                     this.getAccessMask = function() {
                        return this.access;
                        }
                     this.getRightsCount = function() {
                        return rights.length;
                        }
                     this.clearRights = function() {
                        rights.each(function() {
                           this.topic = null}
                        )rights = [];
                        }
                     var prerequisites = [];
                     this.addPrerequisite = function(prerequisite) {
                        if(prerequisite instanceof client.data.Topic.Prerequisite && prerequisites.indexOf(prerequisite) ==- 1) {
                           prerequisites.push(prerequisite);
                           }
                        };
                     this.removePrerequisite = function(prerequisite) {
                        prerequisites.remove(prerequisite);
                        };
                     this.clearPrerequisites = function() {
                        prerequisites = [];
                        };
                     this.checkRequired = function(topic) {
                        var required = false;
                        prerequisites.each(function() {
                           if(this.topic == topic) {
                              required = true; return false; }
                           }
                        );
                        return required;
                        }
                     this.getPrerequisites = function() {
                        return prerequisites.clone();
                        };
                     this.getPrerequisiteCount = function() {
                        return prerequisites.length;
                        };
                     this.isEditable = function() {
                        return(this.access & client.data.TopicAccessConstants.presetWriteAccess) == client.data.TopicAccessConstants.presetWriteAccess;
                        }
                     this.isStructurable = function() {
                        return(this.access & client.data.TopicAccessConstants.presetFullAccess) == client.data.TopicAccessConstants.presetFullAccess;
                        }
                     var steps = [];
                     this.each = function(fn) {
                        for(var i = 0; i < steps.length; i++) {
                           if(fn.call(steps[i]) == false) {
                              return;
                              }
                           }
                        }
                     this.addStep = function(step) {
                        steps.push(step);
                        step.topic = this;
                        };
                     this.removeStep = function(step) {
                        steps.remove(step);
                        step.topic = null;
                        };
                     this.getStep = function(index) {
                        return steps[index];
                        };
                     this.getSteps = function() {
                        return steps.clone();
                        };
                     this.insertStepAt = function(step, index) {
                        steps.splice(index, 0, step);
                        step.topic = this;
                        return;
                        };
                     this.getIndexOfStep = function(step) {
                        return steps.indexOf(step);
                        };
                     this.getStepCount = function() {
                        return steps.length;
                        };
                     this.getAllCards = function() {
                        var allCards = [];
                        for(var i = 0; i < steps.length; i++) {
                           var step = steps[i];
                           allCards.push(step.getMainCard());
                           var lowerCards = step.getCards(client.data.LevelConstants.illustrative);
                           for(var j = 0; j < lowerCards.length; j++) {
                              allCards.push(lowerCards[j]);
                              }
                           var upperCards = step.getCards(client.data.LevelConstants.advanced);
                           for(var j = 0; j < upperCards.length; j++) {
                              allCards.push(upperCards[j]);
                              }
                           }
                        return allCards;
                        }
                     this.getMarkerById = function(id) {
                        for(var i = 0; i < steps.length; i++) {
                           var step = steps[i];
                           var marker = null;
                           marker = step.getMainCard().getMarkerById(id);
                           if(marker)return marker;
                           var upperCards = step.getCards(client.data.LevelConstants.advanced);
                           for(var j = 0; j < upperCards.length; j++) {
                              marker = upperCards[j].getMarkerById(id);
                              if(marker)return marker;
                              }
                           var lowerCards = step.getCards(client.data.LevelConstants.illustrative);
                           for(var j = 0; j < lowerCards.length; j++) {
                              marker = lowerCards[j].getMarkerById(id);
                              if(marker)return marker;
                              }
                           }
                        return null;
                        }
                     this.getCardById = function(id) {
                        for(var i = 0; i < steps.length; i++) {
                           var step = steps[i];
                           var card = null;
                           card = step.getMainCard();
                           if(card.id == id)return card;
                           var upperCards = step.getCards(client.data.LevelConstants.advanced);
                           for(var j = 0; j < upperCards.length; j++) {
                              card = upperCards[j];
                              if(card.id == id)return card;
                              }
                           var lowerCards = step.getCards(client.data.LevelConstants.illustrative);
                           for(var j = 0; j < lowerCards.length; j++) {
                              card = lowerCards[j];
                              if(card.id == id)return card;
                              }
                           }
                        return null;
                        }
                     this.clearSteps = function() {
                        while(steps.length > 0) {
                           this.removeStep(steps[0]);
                           }
                        }
                     this.transformStatic = function() {
                        var prerequisitesCopy = [];
                        this.getPrerequisites().each(function() {
                           var obj = {
                              title : this.title}
                           if(this.topic && this.topic instanceof client.data.Topic) {
                              obj.topic = this.topic.id; }
                           prerequisitesCopy.push(obj); }
                        )var cards = [];
                        for(var index = 0; index < steps.length; index++) {
                           var step = steps[index];
                           step.transformStatic().each(function() {
                              this.index = index; cards.push(this); }
                           );
                           }
                        var obj = {
                           title : this.title, category : this.category, cards : cards, prerequisites : prerequisitesCopy};
                        if(this.id != null) {
                           obj.id = this.id;
                           }
                        return obj;
                        }
                     }
                  client.data.Topic.getAccessLabel = function(access) {
                     var accessRights = lang.NONE;
                     if((access & client.data.TopicAccessConstants.presetReadOnly) == client.data.TopicAccessConstants.presetReadOnly) {
                        accessRights = lang.GROUP_ACCESS_READONLY;
                        if((access & client.data.TopicAccessConstants.presetWriteAccess) == client.data.TopicAccessConstants.presetWriteAccess) {
                           accessRights = lang.GROUP_ACCESS_WRITE;
                           if((access & client.data.TopicAccessConstants.presetFullAccess) == client.data.TopicAccessConstants.presetFullAccess) {
                              accessRights = lang.GROUP_ACCESS_STRUCTURE;
                              if((access & client.data.TopicAccessConstants.owner) == client.data.TopicAccessConstants.owner) {
                                 accessRights = lang.GROUP_ACCESS_OWNER;
                                 }
                              }
                           }
                        }
                     return accessRights;
                     }
                  client.data.Topic.Prerequisite = function(title, topic) {
                     this.title = title;
                     this.topic = topic;
                     }
                  client.data.LevelConstants = {
                     illustrative : 1, main : 2, advanced : 3};
                  client.data.Step = function() {
                     var upperCards = [];
                     var mainCard = null;
                     var lowerCards = [];
                     this.topic = null;
                     this.each = function(level, fn) {
                        var cards = getCards(level);
                        for(var i = 0; i < cards.length; i++) {
                           if(fn.call(cards[i]) == false) {
                              return;
                              }
                           }
                        };
                     this._setArrays = function(upper, main, lower) {
                        upperCards = upper;
                        mainCard = main;
                        lowerCards = lower;
                        }
                     this.clone = function() {
                        var clone = new client.data.Step();
                        clone._setArrays(upperCards.clone(), mainCard, lowerCards.clone());
                        return clone;
                        }
                     this.addCard = function(level, card) {
                        switch(level) {
                           case client.data.LevelConstants.main : if(mainCard) {
                              return;
                              }
                           mainCard = card;
                           card.step = this;
                           break;
                           case client.data.LevelConstants.advanced : upperCards.push(card);
                           card.step = this;
                           break;
                           case client.data.LevelConstants.illustrative : lowerCards.push(card);
                           card.step = this;
                           break;
                           }
                        };
                     this.removeCard = function(card) {
                        if(mainCard == card) {
                           mainCard = null;
                           return;
                           }
                        card.step = null;
                        upperCards.remove(card);
                        lowerCards.remove(card);
                        };
                     this.getMainCard = function() {
                        return mainCard;
                        }
                     function getCards(level) {
                        switch(level) {
                           case client.data.LevelConstants.main : return mainCard ? [mainCard] : [];
                           break;
                           case client.data.LevelConstants.advanced : return upperCards;
                           break;
                           case client.data.LevelConstants.illustrative : return lowerCards;
                           break;
                           default : return upperCards.concat([mainCard], lowerCards);
                           break;
                           }
                        }
                     this.getFirstCard = function(level) {
                        var cards = getCards(level);
                        return cards.length > 0 ? cards[0] : null;
                        };
                     this.getCards = function(level) {
                        return getCards(level).clone();
                        };
                     this.getAllCards = function() {
                        return getCards();
                        };
                     this.insertCardAt = function(level, card, index) {
                        switch(level) {
                           case client.data.LevelConstants.main : this.addCard(level, card);
                           break;
                           case client.data.LevelConstants.advanced : upperCards.splice(index, 0, card);
                           card.step = this;
                           break;
                           case client.data.LevelConstants.illustrative : lowerCards.splice(index, 0, card);
                           card.step = this;
                           break;
                           }
                        return;
                        };
                     this.getIndexOfCard = function(level, card) {
                        switch(level) {
                           case client.data.LevelConstants.main : return 0;
                           break;
                           case client.data.LevelConstants.advanced : return upperCards.indexOf(card);
                           break;
                           case client.data.LevelConstants.illustrative : return lowerCards.indexOf(card);
                           break;
                           }
                        return - 1;
                        };
                     this.getCardCount = function(level) {
                        switch(level) {
                           case client.data.LevelConstants.main : return(mainCard ? 1 : 0);
                           break;
                           case client.data.LevelConstants.advanced : return upperCards.length;
                           break;
                           case client.data.LevelConstants.illustrative : return lowerCards.length;
                           break;
                           default : return(mainCard ? 1 : 0) + lowerCards.length + upperCards.length;
                           break;
                           }
                        };
                     this.getLevelOfCard = function(card) {
                        if(mainCard == card)return client.data.LevelConstants.main;
                        if(upperCards.indexOf(card) >- 1)return client.data.LevelConstants.advanced;
                        if(lowerCards.indexOf(card) >- 1)return client.data.LevelConstants.illustrative;
                        return - 1;
                        }
                     this.getIndex = function() {
                        return this.topic.getIndexOfStep(this);
                        }
                     this.transformStatic = function() {
                        var advanced = getCardCopies(upperCards, client.data.LevelConstants.advanced);
                        var main = getCardCopies([mainCard], client.data.LevelConstants.main);
                        var illustrative = getCardCopies(lowerCards, client.data.LevelConstants.illustrative);
                        return main.concat(advanced, illustrative);
                        function getCardCopies(cards, level) {
                           var cardsCopy = [];
                           cards.each(function() {
                              var skeleton = {
                                 title : this.title, level : level}; if(this.id != null) {
                                 skeleton.id = this.id; }
                              else {
                                 if(this.content != null) {
                                    skeleton.content = this.content; var markers = this.getMarkers(); if(markers) {
                                       skeleton.markers = []; markers.each(function() {
                                          skeleton.markers.push(this.transformStatic()); }
                                       )}
                                    }
                                 }
                              cardsCopy.push(skeleton); }
                           );
                           return cardsCopy;
                           }
                        }
                     }
                  client.data.Card = function() {
                     this.id = null;
                     this.revision = 0;
                     this.checkedOut = false;
                     this.checkOutTime = null;
                     this.title = lang.UNTITLED;
                     this.content = null;
                     this.step = null;
                     var markers = [];
                     this.needsLoading = false;
                     var relations = [];
                     this.isShared = function() {
                        if(!this.step ||!this.step.topic) {
                           return false;
                           }
                        return this.step.topic.isShared();
                        }
                     this.getTitle = function() {
                        return this.title;
                        }
                     this.each = function(fn) {
                        for(var i = 0; i < markers.length; i++) {
                           if(fn.call(markers[i]) == false) {
                              return;
                              }
                           }
                        }
                     this.addMarker = function(marker) {
                        if(marker && markers.indexOf(marker) ==- 1) {
                           marker.card = this;
                           markers.push(marker);
                           }
                        }
                     this.addMarkers = function(markersToAdd) {
                        for(var i = 0; i < markersToAdd.length; i++) {
                           this.addMarker(markersToAdd[i]);
                           }
                        }
                     this.removeMarker = function(marker) {
                        marker.card = null;
                        markers.remove(marker);
                        }
                     this.clearMarkers = function() {
                        for(var i = 0; i < markers.length; i++) {
                           markers[i].card = null;
                           }
                        markers.splice(0, markers.length);
                        }
                     this.getMarkers = function() {
                        return markers.clone();
                        }
                     this.insertMarkerAt = function(marker, index) {
                        marker.card = this;
                        markers.splice(index, 0, marker);
                        }
                     this.getIndexOfMarker = function(marker) {
                        return markers.indexOf(marker);
                        };
                     this.getMarkerCount = function() {
                        return markers.length;
                        };
                     this.getMarkerById = function(id) {
                        for(var i = 0; i < markers.length; i++) {
                           var marker = markers[i];
                           if(marker.id == id)return marker;
                           }
                        return null;
                        }
                     }
                  client.data.Card.LINK_PREFIX = "note://";
                  client.data.User = function() {
                     this.name = "";
                     this.email = "";
                     this.id =- 1;
                     this.settings = {
                        };
                     this.getMembershipInGroup = function(group) {
                        var me = this;
                        var membership = null;
                        group.getMembers().each(function() {
                           if(this.user == me) {
                              membership = this; return false; }
                           }
                        );
                        return membership;
                        }
                     }
                  client.data.GroupAccessConstants = {
                     noAccess : 0, allowView : 1, allowJoin : 2}
                  client.data.Group = function(name, category, description) {
                     this.name = name ? name : lang.UNTITLED;
                     this.description = description ? description : "";
                     this.category = (category && category.length > 0) ? category : lang.NOCAT;
                     this.access = client.data.GroupAccessConstants.noAccess;
                     this.enabled = 0;
                     this.id =- 1;
                     this.type = 0;
                     var members = [];
                     this.addMember = function(member) {
                        if(members.indexOf(member) ==- 1) {
                           member.group = this;
                           members.push(member);
                           }
                        }
                     this.removeMember = function(member) {
                        members.remove(member);
                        member.group = null;
                        }
                     this.getMembers = function() {
                        return members.clone();
                        }
                     this.getMemberByUserId = function(userId) {
                        for(var i = 0; i < members.length; i++) {
                           if(members[i].user.id == userId) {
                              return members[i];
                              }
                           }
                        return null;
                        }
                     this.getMemberCount = function(onlyActive) {
                        if(onlyActive) {
                           var count = 0;
                           members.each(function() {
                              if(this.enabled == 1) {
                                 count++; }
                              }
                           );
                           return count;
                           }
                        else {
                           return members.length;
                           }
                        }
                     this.clearMembers = function() {
                        members.each(function() {
                           this.group = null}
                        )members = [];
                        }
                     }
                  client.data.Group.getAccessLevelLabel = function(accessLevel) {
                     switch(accessLevel) {
                        case client.data.GroupAccessConstants.noAccess : return lang.GROUP_NO_ACCESS;
                        break;
                        case client.data.GroupAccessConstants.allowView : return lang.GROUP_VIEW_ACCESS;
                        break;
                        case client.data.GroupAccessConstants.allowJoin : return lang.GROUP_JOIN_ACCESS;
                        break;
                        default : return"";
                        }
                     }
                  client.data.Right = function(group, access) {
                     this.group = (group !== undefined) ? group : null;
                     this.access = (access !== undefined) ? access : client.data.TopicAccessConstants.noAccess;
                     this.topic = null;
                     }
                  client.data.Relation = function(args) {
                     if(args !== undefined) {
                        this.id = args.id;
                        this.type = args.type;
                        }
                     }
                  client.data.Relation.prototype.clone = function() {
                     var type = client.data.Relation.resolveType(this.type);
                     return new type(this);
                     };
                  client.data.Relation.prototype.id = null;
                  client.data.Relation.prototype.type = null;
                  client.data.Relation.resolveType = function(type) {
                     type = type.substr(0, 1).toUpperCase() + type.substr(1);
                     return client.data[type + "Relation"];
                     }
                  client.data.LinkRelationTypeConstants = {
                     external : 0, platform : 1, note : 2, wikipedia : 3}
                  client.data.LinkRelation = function(args) {
                     client.data.Relation.call(this, args);
                     this.link = args.link ? args.link : "about:blank";
                     this.title = args.title ? args.title : "-";
                     this.type = "link";
                     this.getLinkIcon = function(small) {
                        var type = this.getLinkType();
                        var icon;
                        switch(type) {
                           case client.data.LinkRelationTypeConstants.note : icon = "map-view";
                           break;
                           case client.data.LinkRelationTypeConstants.platform : icon = "link-internal";
                           break;
                           case client.data.LinkRelationTypeConstants.wikipedia : icon = "wikipedia";
                           break;
                           default : icon = "link";
                           }
                        return(small === undefined || small) ? (icon + "-small.png") : (icon + ".png");
                        }
                     this.getLinkTitle = function() {
                        var type = this.getLinkType();
                        switch(type) {
                           case client.data.LinkRelationTypeConstants.note : return lang.NOTE_SHEET + " " + this.title;
                           break;
                           case client.data.LinkRelationTypeConstants.wikipedia : return"Wikipedia: " + this.title;
                           break;
                           default : return this.title;
                           }
                        }
                     this.getLinkType = function() {
                        var url = this.link.toLowerCase();
                        if(url.substr(0, client.data.Card.LINK_PREFIX.length) == client.data.Card.LINK_PREFIX) {
                           return client.data.LinkRelationTypeConstants.note;
                           }
                        else if(url.toLowerCase().search("wikipedia.org") >- 1) {
                           return client.data.LinkRelationTypeConstants.wikipedia;
                           }
                        else if(config.linkPrefix !== undefined && url.toLowerCase().search(config.linkPrefix.toLowerCase()) == 0) {
                           return client.data.LinkRelationTypeConstants.platform;
                           }
                        else {
                           return client.data.LinkRelationTypeConstants.external;
                           }
                        }
                     this.getLinkTarget = function() {
                        var internalPrefix = config.linkPrefix;
                        var internalTarget;
                        if(config.reference !== undefined) {
                           if(config.reference.target !== undefined) {
                              internalTarget = config.reference.target;
                              }
                           else {
                              internalTarget = "_self";
                              }
                           }
                        if(internalPrefix !== undefined && this.link.toLowerCase().search(internalPrefix.toLowerCase()) == 0) {
                           target = internalTarget;
                           }
                        else {
                           target = "_blank";
                           }
                        return target;
                        }
                     }
                  client.data.LinkRelation.prototype = new client.data.Relation;
                  client.data.MembershipLevelConstants = {
                     member : 0, admin : 65535}
                  client.data.MembershipStatusConstants = {
                     requested : 0, active : 1, invited : 2, invitedByAdmin : 3}
                  client.data.Member = function(user, level) {
                     if(user !== undefined) {
                        this.user = user;
                        }
                     if(level !== undefined) {
                        this.level = level;
                        }
                     }
                  client.data.Member.prototype.group = null;
                  client.data.Member.prototype.level = client.data.MembershipLevelConstants.member;
                  client.data.Member.prototype.enabled = client.data.MembershipStatusConstants.requested;
                  client.data.Member.prototype.user = null;
                  client.data.FlashCardResultConstants = {
                     none : 0, right : 1, neutral : 2, wrong : 3};
                  client.data.FlashCard = function(marker) {
                     this.marker = marker;
                     this.box = null;
                     this.level = 0;
                     this.lastTimeAnswered = 0;
                     this.markedForRepetition = false;
                     var lastResults;
                     this.getLastResults = function() {
                        return lastResults.clone();
                        }
                     this.addResult = function(result) {
                        for(var i = 1; i < lastResults.length; i++) {
                           lastResults[i - 1] = lastResults[i];
                           }
                        lastResults[lastResults.length - 1] = result;
                        }
                     this.reset = function() {
                        this.level = 0;
                        lastResults = [client.data.FlashCardResultConstants.none, client.data.FlashCardResultConstants.none, client.data.FlashCardResultConstants.none, client.data.FlashCardResultConstants.none, client.data.FlashCardResultConstants.none];
                        this.markedForRepetition = false;
                        this.lastTimeAnswered = 0;
                        if(this.box) {
                           this.box.removeCard(this);
                           }
                        }
                     this.reset();
                     this.title = null;
                     }
                  client.data.FlashCardBox = function(level) {
                     this.level = level;
                     var cards = [];
                     this.addCard = function(card) {
                        if(cards.indexOf(card) ==- 1) {
                           card.level = this.level;
                           card.box = this;
                           cards.push(card);
                           }
                        }
                     this.removeCard = function(card) {
                        var index = cards.indexOf(card);
                        if(index !=- 1) {
                           cards.splice(index, 1);
                           card.level = 0;
                           card.box = null;
                           }
                        }
                     this.getCard = function(index) {
                        return cards[index];
                        }
                     this.getCardCount = function() {
                        return cards.length;
                        }
                     }
                  client.data.FlashCardContents = function() {
                     this.showCard = false;
                     this.frontSide = null;
                     this.backSide = null;
                     this.showSelfAssessmentButtons = true;
                     }
                  client.data.Notification = function() {
                     }
                  client.data.Notification.prototype.id = undefined;
                  client.data.Notification.prototype.messageType = undefined;
                  client.data.Notification.prototype.feedTitle = undefined;
                  client.data.Notification.prototype.objectId = undefined;
                  client.data.Notification.prototype.objectType = undefined;
                  client.data.Notification.prototype.user = undefined;
                  client.data.Notification.prototype.objectType = undefined;
                  client.ServerInterface = function(args) {
                     var LOGON_FUNCTION;
                     if(args.logonPath) {
                        LOGON_FUNCTION = args.logonPath;
                        }
                     else {
                        LOGON_FUNCTION = "logon.php";
                        }
                     var SESSION_FUNCTION;
                     if(args.sessionPath) {
                        SESSION_FUNCTION = args.sessionPath;
                        }
                     else {
                        SESSION_FUNCTION = "session.php";
                        }
                     var RETRIEVE_PASSWORD_ACTION = "retrievepassword";
                     var SIGN_UP_ACTION = "signup";
                     var SIGN_IN_ACTION = "signin";
                     var DELETE_ACCOUNT_ACTION = "deleteAccount";
                     var CHANGE_PASS_ACTION = "changePass";
                     var SIGN_OUT_ACTION = "signout";
                     var CHECK_CARD_REVISION_ACTION = "checkCardRevision";
                     var CHECK_OUT_CARD_ACTION = "checkOutCard";
                     var CHECK_IN_CARD_ACTION = "checkInCard";
                     var LOAD_CARDS_ACTION = "loadCards";
                     var UPDATE_TOPIC_ACTION = "updateTopic";
                     var UPDATE_TOPIC_LICENSE_ACTION = "updateTopicLicense";
                     var UPDATE_CARD_ACTION = "updateCard";
                     var UPDATE_MARKERS_ACTION = "updateMarkers";
                     var UPDATE_TRAINING_SESSION_ACTION = "updateTrainingSession";
                     var REPORT_ABUSE_ACTION = "reportAbuse";
                     var SUGGEST_FEATURE_ACTION = "suggestFeature";
                     var LOCK_CARD_ACTION = "lockCard";
                     var LOAD_TOPIC_LIST_ACTION = "loadTopicList";
                     var CHECK_TOPIC_REVISION_ACTION = "checkTopicRevision";
                     var LOAD_GROUPS_ACTION = "loadGroups";
                     var UPDATE_GROUP_ACTION = "updateGroup";
                     var CREATE_GROUP_ACTION = "createGroup";
                     var INVITE_TO_GROUP_ACTION = "inviteToGroup";
                     var JOIN_GROUP_ACTION = "joinGroup";
                     var UPDATE_MEMBER_ACTION = "updateMember";
                     var LEAVE_GROUP_ACTION = "leaveGroup";
                     var SHARE_TOPIC_ACTION = "shareTopic";
                     var KEEP_ALIVE_ACTION = "keepAlive";
                     var KEEP_ALIVE_INTERVAL = utility.keepAliveInterval !== undefined ? utility.keepAliveInterval : 720;
                     var LOAD_TOPIC_ACTION = "loadTopic";
                     var PUBLISH_TOPIC_ACTION = "publishTopic";
                     var DELETE_TOPICS_ACTION = "deleteTopics";
                     var LOAD_NOTIFICATIONS_ACTION = "loadNotifications";
                     var MARK_NOTIFICATION_AS_READ_ACTION = "markNotificationAsRead";
                     var GET_CARDS_WITH_MARKER_ACTION = "getCardsWithMarker";
                     var INVITE_USER_ACTION = "inviteUser";
                     var SEARCH_DATABASE_ACTION = "searchDatabase";
                     var CHECK_EQUATION_SUPPORT_ACTION = "checkEquationSupport";
                     var RENDER_EQUATION_ACTION = "renderEquation";
                     var currentUser;
                     var displayPlugins;
                     this.addDisplayPlugin = function(display) {
                        displayPlugins.push(display);
                        }
                     this.removeDisplayPlugin = function(display) {
                        displayPlugins.splice(displayPlugins.indexOf(display), 1);
                        }
                     this.getDisplayPlugins = function() {
                        return displayPlugins.clone();
                        }
                     var markerPlugins;
                     this.addMarkerPlugin = function(marker) {
                        markerPlugins.push(marker);
                        }
                     this.removeMarkerPlugin = function(marker) {
                        markerPlugins.splice(markerPlugins.indexOf(marker), 1);
                        }
                     this.getMarkerPlugins = function() {
                        return markerPlugins.clone();
                        }
                     var requestCache;
                     this.enableRequestCache = function() {
                        alert("unfinished");
                        cacheRequests = true;
                        requestCache = [];
                        }
                     this.sendCachedRequests = function(disable) {
                        alert("unfinished");
                        var cachedItems = requestCache.clone();
                        var cacheRequest = {
                           }
                        cachedItems.each(function() {
                           }
                        );
                        if(disable) {
                           this.disableRequestCache();
                           }
                        }
                     this.disableRequestCache = function() {
                        alert("unfinished");
                        cacheRequests = false;
                        }
                     var me;
                     var cacheRequests;
                     function sendRequest(url, data, tag, type, callback) {
                        showLoader();
                        if(config.customArgs !== undefined && typeof config.customArgs == "object") {
                           data = $.extend($.extend( {
                              }
                           , config.customArgs), data);
                           }
                        var dataRenamed = {
                           };
                        for(var i in data) {
                           dataRenamed[prefixKey(i)] = data[i];
                           }
                        if(currentUser != null) {
                           var settingsString = JSON.stringify(currentUser.settings);
                           if(settingsString != lastSettingsUpdate) {
                              dataRenamed[prefixKey("settings")] = lastSettingsUpdate = settingsString;
                              }
                           }
                        var tag = {
                           url : url, args : data, action : data.action, data : tag, callback : callback, type : type};
                        var obj = {
                           type : "POST", url : url, data : dataRenamed, success : asyncReply, error : asyncError, dataType : type, getTag : function() {
                              return arguments.callee.tag;
                              }
                           };
                        obj.getTag.tag = tag;
                        if(cacheRequests === true) {
                           requestCache.push(obj);
                           return false;
                           }
                        else {
                           return _sendRequest(obj);
                           }
                        function prefixKey(i) {
                           return config.prefixData ? 'data[' + i + ']':i;
                           }
                        }
                     function _sendRequest(obj) {
                        startKeepAlive();
                        return jQuery.ajax(obj);
                        }
                     function asyncReply(data) {
                        hideLoader();
                        if(data == null) {
                           data = {
                              error : "undefined"};
                           }
                        var item = this.getTag();
                        if(data.error !== undefined) {
                           data.tag = item.data;
                           utility.triggerCallback(item.callback, data);
                           }
                        else {
                           switch(item.url) {
                              case LOGON_FUNCTION : logonHandler(data, item);
                              break;
                              case SESSION_FUNCTION : sessionHandler(data, item);
                              break;
                              }
                           }
                        }
                     function asyncError(XMLHttpRequest, textStatus, errorThrown) {
                        hideLoader();
                        var item = this.getTag();
                        if(item !== undefined) {
                           var ret = utility.triggerCallback(item.callback, {
                              error : "connection", tag : item.data}
                           );
                           if(item.url == SESSION_FUNCTION) {
                              switch(item.action) {
                                 case UPDATE_CARD_ACTION : case UPDATE_MARKERS_ACTION : resetCardRequest(item.data, false);
                                 if(ret) {
                                    return;
                                    }
                                 break;
                                 }
                              }
                           }
                        if(XMLHttpRequest.status === undefined || XMLHttpRequest.status == 404) {
                           alert(lang.CONNECTION_ERROR);
                           }
                        else if(XMLHttpRequest.status == 0) {
                           alert(lang.SESSION_ABORTED);
                           }
                        else {
                           alert(lang.SESSION_INVALID);
                           }
                        }
                     function customReply(data) {
                        if(this._success) {
                           utility.triggerCallback(this._success, [data, this._tag]);
                           }
                        }
                     function customError(data) {
                        if(this._error) {
                           utility.triggerCallback(this._error, [data, this._tag]);
                           }
                        }
                     var loader;
                     var loadCount = 0;
                     function showLoader() {
                        if(!loader) {
                           loader = $(document.createElement("div")).addClass("loader main").appendTo(document.body);
                           }
                        loadCount++;
                        loader.addClass("show");
                        }
                     function hideLoader() {
                        loadCount--;
                        if(loadCount < 1) {
                           loader.removeClass("show");
                           loadCount = 0;
                           }
                        }
                     var keepAliveCall;
                     function startKeepAlive() {
                        window.clearTimeout(keepAliveCall);
                        keepAliveCall = window.setTimeout(sendKeepAlive, KEEP_ALIVE_INTERVAL * 1000);
                        }
                     function getKeepAlive() {
                        var obj = {
                           action : KEEP_ALIVE_ACTION};
                        return obj;
                        }
                     function sendKeepAlive() {
                        var obj = getKeepAlive();
                        return sendRequest(SESSION_FUNCTION, obj, null, "json", utility.createCallback(me, keepAliveCallback));
                        }
                     function keepAliveCallback(data) {
                        if(!data.success) {
                           stopKeepAlive();
                           alert(lang.CONNECTION_DOWN);
                           }
                        }
                     function stopKeepAlive() {
                        window.clearTimeout(keepAliveCall);
                        }
                     this.callCustom = function(func, data, success, error, tag, type) {
                        if(config.customArgs !== undefined && typeof config.customArgs == "object") {
                           data = $.extend($.extend( {
                              }
                           , config.customArgs), data);
                           }
                        var arguments = {
                           type : "POST", url : func, data : data, success : customReply, error : customError, dataType : type ? type : "json", _success : success, _error : error, _tag : tag}
                        jQuery.ajax(arguments);
                        }
                     var currentUser;
                     this.getCurrentUser = function() {
                        return currentUser;
                        }
                     this.resumeSession = function(user) {
                        currentUser = user;
                        startKeepAlive();
                        }
                     var topics;
                     function getTopic(id) {
                        var topic = null;
                        topics.each(function() {
                           if(this.id == id) {
                              topic = this; return false; }
                           return true; }
                        )if(topic == null) {
                           topic = new client.data.Topic();
                           topic.id = id;
                           topics.push(topic);
                           }
                        return topic;
                        }
                     this.getTopics = function() {
                        return topics.clone();
                        }
                     this.getTopicCount = function() {
                        return topics.length;
                        }
                     var standardLevel = 1;
                     var minLevel = 1;
                     this.minBoxLevel = minLevel;
                     var maxLevel = 4;
                     this.maxBoxLevel = maxLevel;
                     var flashCardBoxes;
                     this.getFlashCardBoxes = function() {
                        return flashCardBoxes.clone();
                        }
                     this.getFlashCardBox = function(level) {
                        for(var i = 0; i < flashCardBoxes.length; i++) {
                           var flashCardBox = flashCardBoxes[i];
                           if(flashCardBox.level == level) {
                              return flashCardBox;
                              }
                           }
                        return null;
                        }
                     this.moveFlashCardToLevel = function(flashCard, level) {
                        if(flashCard.level == level)return;
                        if(level < minLevel || level > maxLevel)return;
                        var destinationBox = this.getFlashCardBox(level);
                        if(!destinationBox)return;
                        var currentBox = flashCard.box;
                        if(currentBox) {
                           currentBox.removeCard(flashCard);
                           }
                        destinationBox.addCard(flashCard);
                        }
                     this.addFlashCard = function(flashCard) {
                        if(!flashCard.box) {
                           flashCardBoxes[0].addCard(flashCard);
                           }
                        }
                     this.removeFlashCard = function(flashCard) {
                        if(flashCard && flashCard.box) {
                           flashCard.box.removeCard(flashCard);
                           }
                        }
                     this.updateTrainingSession = function(markers, callback) {
                        var allFlashCards = [];
                        for(var i = 0; i < markers.length; i++) {
                           var marker = markers[i];
                           var flashCards = marker.flashCards;
                           if(flashCards) {
                              for(var j = 0; j < flashCards.length; j++) {
                                 var flashCard = flashCards[j];
                                 allFlashCards.push( {
                                    number : j, results : flashCard.getLastResults(), level : flashCard.level, marker : marker.id, lastTimeAnswered : flashCard.lastTimeAnswered, markedForRepetition : flashCard.markedForRepetition == true ? 1 : 0}
                                 );
                                 }
                              }
                           }
                        return sendRequest(SESSION_FUNCTION, {
                           action : UPDATE_TRAINING_SESSION_ACTION, trainingSession : JSON.stringify(allFlashCards)}
                        , null, "json", callback);
                        }
                     var events;
                     this.registerSyncHandler = function(type, syncHandler) {
                        if(events[type] !== undefined) {
                           if(events[type].indexOf(syncHandler) ==- 1) {
                              events[type].push(syncHandler);
                              }
                           }
                        }
                     this.unregisterSyncHandler = function(type, syncHandler) {
                        if(events[type] !== undefined) {
                           events[type].remove(syncHandler);
                           }
                        }
                     var groups;
                     this.getGroups = function() {
                        return groups.clone();
                        }
                     this.memberOfAnyGroup = function() {
                        var isMember = false;
                        groups.each(function() {
                           if(currentUser.getMembershipInGroup(this) != null) {
                              isMember = true; return false; }
                           }
                        );
                        return isMember;
                        }
                     function getGroup(id, noCreate) {
                        var group = null;
                        groups.each(function() {
                           if(this.id == id) {
                              group = this; return false; }
                           return true; }
                        )if(group == null && noCreate === undefined) {
                           group = new client.data.Group();
                           group.id = id;
                           groups.push(group);
                           }
                        return group;
                        }
                     this.getGroup = function(id) {
                        return getGroup(id, true)};
                     this.loadGroupList = function(callback, includeKnown) {
                        var obj = {
                           action : LOAD_GROUPS_ACTION};
                        if(includeKnown) {
                           obj.includeKnown = true;
                           }
                        return sendRequest(SESSION_FUNCTION, obj, null, "json", callback);
                        }
                     this.createGroup = function(group, callback) {
                        return sendRequest(SESSION_FUNCTION, {
                           action : CREATE_GROUP_ACTION, group : JSON.stringify( {
                              name : group.name, category : group.category, description : group.description, access : group.access}
                           )}
                        , null, "json", callback);
                        }
                     this.inviteToGroup = function(group, userIds, externalIds, names, callback) {
                        var obj = {
                           action : INVITE_TO_GROUP_ACTION, group : group.id};
                        if(userIds !== undefined && userIds.length > 0) {
                           obj.ids = userIds.join(",");
                           }
                        if(externalIds !== undefined && externalIds.length > 0) {
                           obj.externalIds = externalIds.join(",");
                           }
                        if(names !== undefined && names.length > 0) {
                           obj.names = names.join(",");
                           }
                        return sendRequest(SESSION_FUNCTION, obj, group, "json", callback);
                        }
                     this.leaveGroup = function(group, callback) {
                        return sendRequest(SESSION_FUNCTION, {
                           action : LEAVE_GROUP_ACTION, id : group.id}
                        , group, "json", callback);
                        }
                     this.joinGroup = function(group, callback) {
                        return sendRequest(SESSION_FUNCTION, {
                           action : JOIN_GROUP_ACTION, id : group.id}
                        , group, "json", callback);
                        }
                     this.updateMembership = function(membership, level, enabled, callback) {
                        return sendRequest(SESSION_FUNCTION, {
                           action : UPDATE_MEMBER_ACTION, group : membership.group.id, user : membership.user.id, level : level, enabled : enabled ? 1 : 0}
                        , membership, "json", callback);
                        }
                     this.cancelMembership = function(membership, callback) {
                        return this.updateMembership(membership, - 1, false, callback);
                        }
                     this.acceptMembership = function(membership, reject, callback) {
                        return this.updateMembership(membership, reject ?- 1 : 0, true, callback);
                        }
                     this.updateGroup = function(group, properties, callback) {
                        if(properties.name === undefined) {
                           properties.name = group.name;
                           }
                        if(properties.description === undefined) {
                           properties.description = group.description;
                           }
                        if(properties.access === undefined) {
                           properties.access = group.access;
                           }
                        if(properties.category === undefined) {
                           properties.category = group.category;
                           }
                        return sendRequest(SESSION_FUNCTION, {
                           action : UPDATE_GROUP_ACTION, id : group.id, group : JSON.stringify(properties)}
                        , group, "json", callback);
                        }
                     this.shareTopic = function(topic, group, access, callback) {
                        var obj = {
                           action : SHARE_TOPIC_ACTION, topic : topic.id, group : group.id, mask : access};
                        return sendRequest(SESSION_FUNCTION, obj, {
                           topic : topic, group : group}
                        , "json", callback);
                        }
                     this.updateRight = function(right, access, callback) {
                        return sendRequest(SESSION_FUNCTION, {
                           action : SHARE_TOPIC_ACTION, topic : right.topic.id, group : right.group.id, mask : access}
                        , {
                           right : right}
                        , "json", callback);
                        }
                     this.reportAbuse = function(type, id, callback) {
                        return sendRequest(SESSION_FUNCTION, {
                           action : REPORT_ABUSE_ACTION, type : type, id : id}
                        , null, "json", callback);
                        }
                     this.suggestFeature = function(description, callback) {
                        return sendRequest(SESSION_FUNCTION, {
                           action : SUGGEST_FEATURE_ACTION, description : description}
                        , null, "json", callback);
                        }
                     var notifications;
                     this.loadNotifications = function(callback) {
                        return sendRequest(SESSION_FUNCTION, {
                           action : LOAD_NOTIFICATIONS_ACTION}
                        , null, "json", callback);
                        }
                     this.markNotificationAsRead = function(notification, callback) {
                        return sendRequest(SESSION_FUNCTION, {
                           action : MARK_NOTIFICATION_AS_READ_ACTION, id : notification.id}
                        , notification, "json", callback);
                        }
                     this.getNotifications = function() {
                        return notifications.clone();
                        }
                     this.getNotificationCount = function() {
                        return notifications.length;
                        }
                     this.getCardsWithMarker = function(tool, callback) {
                        return sendRequest(SESSION_FUNCTION, {
                           action : GET_CARDS_WITH_MARKER_ACTION, tool : tool}
                        , null, "json", callback);
                        }
                     this.retrievePassword = function(nameOrEmail, captcha, callback) {
                        return sendRequest(LOGON_FUNCTION, {
                           email : nameOrEmail, captcha : captcha, action : RETRIEVE_PASSWORD_ACTION}
                        , null, "json", callback);
                        }
                     this.signUp = function(name, password, email, captcha, callback) {
                        return sendRequest(LOGON_FUNCTION, {
                           name : name, password : password, email : email, captcha : captcha, action : SIGN_UP_ACTION}
                        , null, "json", callback);
                        }
                     this.openSession = function(name, password, callback) {
                        return sendRequest(LOGON_FUNCTION, {
                           name : name, password : password, action : SIGN_IN_ACTION}
                        , null, "json", callback);
                        }
                     this.changePassword = function(current, newpass, callback) {
                        return sendRequest(SESSION_FUNCTION, {
                           current : current, newpass : newpass, action : CHANGE_PASS_ACTION}
                        , null, "json", callback);
                        }
                     this.deleteAccount = function(current, callback) {
                        return sendRequest(SESSION_FUNCTION, {
                           current : current, action : DELETE_ACCOUNT_ACTION}
                        , null, "json", callback);
                        }
                     this.loadTopicList = function(callback) {
                        return sendRequest(SESSION_FUNCTION, {
                           action : LOAD_TOPIC_LIST_ACTION}
                        , null, "json", callback);
                        }
                     this.updateTopic = function(topic, data, callback) {
                        var obj = {
                           action : UPDATE_TOPIC_ACTION, topic : JSON.stringify(data)};
                        if((topic) && topic.id !== undefined && topic.id != null) {
                           obj.id = topic.id;
                           }
                        return sendRequest(SESSION_FUNCTION, obj, null, "json", callback);
                        }
                     this.checkTopicRevision = function(topic, callback) {
                        var obj = {
                           action : CHECK_TOPIC_REVISION_ACTION, revision : topic.revision, id : topic.id}
                        return sendRequest(SESSION_FUNCTION, obj, topic, "json", callback)}
                     this.updateTopicLicense = function(topic, license, callback) {
                        return sendRequest(SESSION_FUNCTION, {
                           action : UPDATE_TOPIC_LICENSE_ACTION, id : topic.id, license : license}
                        , topic, "json", callback);
                        }
                     this.deleteTopics = function(topics, callback) {
                        var ids = [];
                        topics.each(function() {
                           ids.push(this.id); }
                        )return sendRequest(SESSION_FUNCTION, {
                           action : DELETE_TOPICS_ACTION, ids : ids.join(",")}
                        , topics.clone(), "json", callback);
                        }
                     this.restoreTopicRaw = function(topicRemote) {
                        var topic = new client.data.Topic();
                        syncFunctions.copyPropertiesFuncTopic(topicRemote, topic);
                        correctPrerequisites(topic);
                        return topic;
                        }
                     function correctPrerequisites(topic) {
                        topic.getPrerequisites().each(function() {
                           if(this.topic !== undefined && this.topic !== null &&!(this.topic instanceof client.data.Topic)) {
                              var topic = null; var id = this.topic; topics.each(function() {
                                 if(this.id == id) {
                                    topic = this; return false; }
                                 return true; }
                              ); if(topic) {
                                 this.topic = topic; }
                              }
                           }
                        )}
                     this.checkCardRevision = function(card, callback) {
                        return sendRequest(SESSION_FUNCTION, {
                           action : CHECK_CARD_REVISION_ACTION, id : card.id, revision : card.revision}
                        , card, "json", callback);
                        }
                     this.checkOutCard = function(card, callback) {
                        return sendRequest(SESSION_FUNCTION, {
                           action : CHECK_OUT_CARD_ACTION, id : card.id}
                        , card, "json", callback);
                        }
                     this.checkInCard = function(card, callback) {
                        return sendRequest(SESSION_FUNCTION, {
                           action : CHECK_IN_CARD_ACTION, id : card.id}
                        , card, "json", callback);
                        }
                     this.loadCardsContents = function(cards, callback) {
                        var idArray = [];
                        for(var i = 0; i < cards.length; i++) {
                           idArray.push(cards[i].id);
                           }
                        return sendRequest(SESSION_FUNCTION, {
                           action : LOAD_CARDS_ACTION, ids : idArray.join(",")}
                        , cards.clone(), "json", callback);
                        }
                     this.loadCardContents = function(card, callback) {
                        return this.loadCardsContents([card], callback);
                        }
                     this.updateCard = function(card, data, callback) {
                        var obj = {
                           action : UPDATE_CARD_ACTION, id : card.id};
                        if(data.title !== undefined) {
                           obj.title = data.title;
                           }
                        if(data.content !== undefined) {
                           obj.content = data.content;
                           }
                        if(data.markers !== undefined) {
                           obj.markers = JSON.stringify(data.markers);
                           }
                        if(data.deletedMarkerIds !== undefined) {
                           obj.deletedMarkerIds = JSON.stringify(data.deletedMarkerIds);
                           }
                        return this.resendUpdateRequest(card, obj, callback);
                        }
                     this.updateMarkers = function(card, markers, deletedMarkerIds, callback) {
                        var obj = {
                           action : UPDATE_MARKERS_ACTION, id : card.id, markers : JSON.stringify(markers)};
                        if(deletedMarkerIds !== undefined) {
                           obj.deletedMarkerIds = JSON.stringify(deletedMarkerIds);
                           }
                        return this.resendUpdateRequest(card, obj, callback);
                        }
                     this.resendUpdateRequest = function(card, obj, callback) {
                        var request = sendRequest(SESSION_FUNCTION, obj, card, "json", callback);
                        if(request.readyState != 4 || request.status != 200) {
                           card.request = request;
                           card.requestData = obj;
                           }
                        return request;
                        }
                     this.abortUpdate = function(card) {
                        if(card.request) {
                           try {
                              card.request.abort();
                              }
                           catch(e) {
                              }
                           hideLoader();
                           resetCardRequest(card);
                           }
                        }
                     function resetCardRequest(card) {
                        if(card.request) {
                           delete card.request;
                           delete card.requestData;
                           }
                        }
                     this.inviteUser = function(email, inviter, callback) {
                        var obj = {
                           action : INVITE_USER_ACTION, email : email, inviter : inviter}
                        return sendRequest(SESSION_FUNCTION, obj, null, "json", callback);
                        }
                     this.searchDatabase = function(query, type, callback) {
                        return sendRequest(SESSION_FUNCTION, {
                           action : SEARCH_DATABASE_ACTION, type : type, query : query}
                        , null, "json", callback);
                        }
                     this.abortRequest = function(request) {
                        try {
                           if(request !== undefined) {
                              request.abort();
                              hideLoader();
                              }
                           }
                        catch(ex) {
                           }
                        }
                     this.checkEquationSupport = function(callback) {
                        return sendRequest(SESSION_FUNCTION, {
                           action : CHECK_EQUATION_SUPPORT_ACTION}
                        , null, "json", callback);
                        }
                     this.renderEquation = function(code, topic, callback) {
                        return sendRequest(SESSION_FUNCTION, {
                           action : RENDER_EQUATION_ACTION, topic : topic.id, equation : code}
                        , null, "json", callback);
                        }
                     function getRelationType(relation) {
                        if(relation instanceof client.data.Card) {
                           return 1;
                           }
                        if(relation instanceof client.data.Marker) {
                           return 2;
                           }
                        return 0;
                        }
                     function getRelation(relationId, relationType) {
                        var val = null;
                        if(relationType == 1) {
                           topics.each(function() {
                              this.each(function() {
                                 this.each(undefined, function() {
                                    if(relationType == 1 && this.id == relationId) {
                                       val = this; return false; }
                                    if(relationType == 2) {
                                       this.each(function() {
                                          if(this.id == relationId) {
                                             val = this; return false; }
                                          }
                                       )}
                                    if(val) {
                                       return false; }
                                    }
                                 ); if(val) {
                                    return false; }
                                 }
                              ); }
                           );
                           return val;
                           }
                        }
                     function getCard(id) {
                        return getRelation(id, 1);
                        }
                     this.closeSession = function(callback) {
                        return sendRequest(LOGON_FUNCTION, {
                           action : SIGN_OUT_ACTION}
                        , null, "json", callback);
                        }
                     var lastSettingsUpdate;
                     function logonHandler(data, item) {
                        switch(item.action) {
                           case RETRIEVE_PASSWORD_ACTION : break;
                           case SIGN_UP_ACTION : break;
                           case SIGN_IN_ACTION : currentUser = new client.data.User();
                           currentUser.name = data.name;
                           currentUser.id = data.id;
                           var settings = data.settings;
                           if(settings === undefined || settings == null || settings == "") {
                              currentUser.settings = {
                                 };
                              }
                           else {
                              currentUser.settings = JSON.parse(settings);
                              }
                           lastSettingsUpdate = JSON.stringify(currentUser.settings);
                           startKeepAlive();
                           break;
                           case SIGN_OUT_ACTION : stopKeepAlive();
                           initialize.call(me, null);
                           break;
                           }
                        utility.triggerCallback(item.callback, {
                           success : true}
                        );
                        }
                     var syncFunctions = {
                        copyPropertiesFuncTopic : function(from, to) {
                           to.id = from.id;
                           to.title = from.title;
                           to.category = from.category;
                           to.author = getUser(from.author);
                           to.license = from.license;
                           to.access = from.access;
                           to.revision = from.revision;
                           if(from.prerequisites) {
                              to.clearPrerequisites();
                              from.prerequisites.each(function() {
                                 if(this.topic !== undefined) {
                                    var id = this.topic; var prereqTopic = null; topics.each(function() {
                                       if(this.id == id) {
                                          prereqTopic = this; return false; }
                                       return true; }
                                    ); if(prereqTopic == null) {
                                       prereqTopic = this.topic; }
                                    }
                                 to.addPrerequisite(new client.data.Topic.Prerequisite(this.title, prereqTopic)); }
                              )}
                           if(from.rights) {
                              to.clearRights();
                              from.rights.each(function() {
                                 to.addRight(new client.data.Right(getGroup(this.group), this.mask)); }
                              )}
                           var cards = to.getAllCards();
                           to.clearSteps();
                           from.cards.each(function() {
                              var remote = this; var local = null; cards.each(function() {
                                 if(this.id == remote.id) {
                                    syncFunctions.copyPropertiesFuncCard(remote, local = this); return false; }
                                 return true; }
                              ); if(local == null) {
                                 local = syncFunctions.createNewItemFuncCard(remote); }
                              var stepCount = to.getStepCount(); while(stepCount < remote.index + 1) {
                                 to.addStep(new client.data.Step()); stepCount++; }
                              to.getStep(remote.index).addCard(remote.level, local); }
                           );
                           cards = null;
                           }
                        , copyPropertiesFuncStep : function(from, to) {
                           to.id = from.id;
                           var levels = [client.data.LevelConstants.advanced, client.data.LevelConstants.main, client.data.LevelConstants.illustrative];
                           var remoteArrays = [from.advanced, from.main, from.illustrative];
                           for(var i = 0; i < levels.length; i++) {
                              var level = levels[i];
                              var remoteArray = remoteArrays[i];
                              if(remoteArray) {
                                 var items = to.getCards(level);
                                 utility.createShadowedArray(items, function(item, index) {
                                    to.insertCardAt(level, item, index); }
                                 , function(index) {
                                    to.removeCard(items[index]); }
                                 );
                                 utility.syncListsUnordered(items, remoteArray, syncFunctions.copyPropertiesFuncCard, syncFunctions.createNewItemFuncCard, syncFunctions.isSameItemFunc, syncFunctions.isItemProtectedFunc);
                                 }
                              }
                           }
                        , copyPropertiesFuncCard : function(from, to) {
                           delete to.id;
                           to.id = from.id;
                           to.revision = from.revision;
                           to.title = from.title;
                           }
                        , copyPropertiesFuncMarker : function(from, to) {
                           to.id = from.id;
                           to.shared = from.shared;
                           to.notify = from.notify;
                           to.revision = from.revision;
                           if(from.flashCards != null) {
                              to.flashCards = [];
                              for(var i = 0; i < from.flashCards.length; i++) {
                                 var flashCardRemote = from.flashCards[i];
                                 var flashCard = new client.data.FlashCard(to);
                                 flashCard.markedForRepetition = flashCardRemote.markedForRepetition;
                                 flashCard.lastTimeAnswered = flashCardRemote.lastTimeAnswered;
                                 for(var j = 0; j < 5; j++) {
                                    flashCard.addResult(flashCardRemote.results[j]);
                                    }
                                 to.flashCards.push(flashCard);
                                 me.moveFlashCardToLevel(flashCard, flashCardRemote.level);
                                 }
                              }
                           if(from.relations !== undefined) {
                              to.clearRelations();
                              from.relations.each(function() {
                                 var remoteRelation = this; var type = client.data.Relation.resolveType(remoteRelation.type); var relation = new type(remoteRelation); to.relationsStore.push(relation); }
                              );
                              }
                           if(from.data != null) {
                              to.dataStore = JSON.parse(from.data);
                              }
                           if(from.range != null) {
                              to.rangeStore = from.range;
                              }
                           to.user = getUser(from.user);
                           to.isMine = (to.user === currentUser);
                           to.restore();
                           }
                        , createNewItemFuncTopic : function(from) {
                           var topic = new client.data.Topic();
                           topic.id = from.id;
                           syncFunctions.copyPropertiesFuncTopic(from, topic);
                           return topic;
                           }
                        , createNewItemFuncStep : function(from) {
                           var step = new client.data.Step();
                           syncFunctions.copyPropertiesFuncStep(from, step);
                           return step;
                           }
                        , createNewItemFuncCard : function(from) {
                           var card = new client.data.Card();
                           if(from.content !== undefined) {
                              card.content = from.content;
                              if(from.markers) {
                                 from.markers.each(function() {
                                    card.addMarker(syncFunctions.createNewItemFuncMarker(this)); }
                                 );
                                 }
                              }
                           else {
                              card.needsLoading = true;
                              }
                           syncFunctions.copyPropertiesFuncCard(from, card);
                           return card;
                           }
                        , createNewItemFuncMarker : function(from) {
                           for(var i = 0; i < markerPlugins.length; i++) {
                              var markerPlugin = markerPlugins[i];
                              var marker;
                              if(markerPlugin.tool == from.tool) {
                                 marker = markerPlugin.createNew();
                                 marker.id = from.id;
                                 }
                              if(marker) {
                                 syncFunctions.copyPropertiesFuncMarker(from, marker);
                                 return marker;
                                 }
                              }
                           return null;
                           }
                        , isSameItemFunc : function(local, remote) {
                           return local.id == remote.id;
                           }
                        , isItemProtectedFunc : function(local) {
                           return local.id == null;
                           }
                        }
                     var users;
                     function getUser(id) {
                        if(currentUser.id == id) {
                           return currentUser;
                           }
                        var user;
                        users.each(function() {
                           if(this.id == id) {
                              user = this; return false; }
                           return true; }
                        )if(user !== undefined) {
                           return user;
                           }
                        users.push(user = new client.data.User());
                        user.id = id;
                        user.name = null;
                        return user;
                        }
                     this.getUsers = function() {
                        return users.clone();
                        }
                     var externalUsers;
                     this.getExternalUsers = function() {
                        return externalUsers.clone();
                        }
                     function sessionHandler(data, item) {
                        switch(item.action) {
                           case LOAD_TOPIC_LIST_ACTION : if(data.topics) {
                              utility.syncListsUnordered(topics, data.topics, syncFunctions.copyPropertiesFuncTopic, syncFunctions.createNewItemFuncTopic, syncFunctions.isSameItemFunc, syncFunctions.isItemProtectedFunc);
                              }
                           topics.each(function() {
                              correctPrerequisites(this)}
                           );
                           events["topics"].each(function() {
                              this.call(window, {
                                 type : "topics", data : topics.clone()}
                              ); }
                           );
                           break;
                           case CHECK_TOPIC_REVISION_ACTION : if(data.topic === undefined) {
                              break;
                              }
                           else {
                              }
                           case UPDATE_TOPIC_ACTION : var topic = getTopic(data.topic.id);
                           syncFunctions.copyPropertiesFuncTopic(data.topic, topic);
                           correctPrerequisites(topic);
                           utility.triggerCallback(item.callback, {
                              success : true, reverted : data.reverted, topic : topic}
                           );
                           events["topic"].each(function() {
                              this.call(window, {
                                 type : "topic", data : topic}
                              ); }
                           );
                           return;
                           break;
                           case UPDATE_TOPIC_LICENSE_ACTION : item.data.license = data.license;
                           utility.triggerCallback(item.callback, {
                              success : true, topic : item.data}
                           );
                           events["topic"].each(function() {
                              this.call(window, {
                                 type : "topic", data : item.data}
                              ); }
                           );
                           return;
                           break;
                           case DELETE_TOPICS_ACTION : var ids = [];
                           item.data.each(function() {
                              topics.remove(this); ids.push(this.id); }
                           );
                           utility.triggerCallback(item.callback, {
                              success : true, deletedIds : ids}
                           );
                           events["topics"].each(function() {
                              this.call(window, {
                                 type : "topics", data : topics.clone()}
                              ); }
                           );
                           return;
                           break;
                           case CHECK_CARD_REVISION_ACTION : if(data.cards === undefined) {
                              break;
                              }
                           else {
                              item.data = [item.data];
                              }
                           case LOAD_CARDS_ACTION : var cards = item.data;
                           for(var i = 0; i < cards.length; i++) {
                              var card = cards[i];
                              var cardRemote = data.cards[i];
                              var newer = (cardRemote.revision > card.revision);
                              if(card.needsLoading == true || newer) {
                                 card.title = cardRemote.title;
                                 if(cardRemote.content) {
                                    card.content = cardRemote.content;
                                    card.revision = cardRemote.revision;
                                    }
                                 if(card.getMarkerCount() == 0 || newer) {
                                    card.clearMarkers();
                                    if(cardRemote.markers) {
                                       for(var j = 0; j < cardRemote.markers.length; j++) {
                                          var markerRestore = cardRemote.markers[j];
                                          var marker = syncFunctions.createNewItemFuncMarker(markerRestore);
                                          card.addMarker(marker);
                                          }
                                       }
                                    }
                                 card.needsLoading = false;
                                 }
                              }
                           utility.triggerCallback(item.callback, {
                              success : true, cards : cards}
                           );
                           events["cards"].each(function() {
                              this.call(window, {
                                 type : "cards", data : cards}
                              ); }
                           );
                           return;
                           break;
                           case CHECK_OUT_CARD_ACTION : var card = item.data;
                           card.checkedOut = true;
                           card.checkOutTime = new Date();
                           utility.triggerCallback(item.callback, {
                              success : true, revision : data.revision, card : card}
                           );
                           return;
                           break;
                           case CHECK_IN_CARD_ACTION : var card = item.data;
                           card.checkedOut = false;
                           card.checkOutTime = null;
                           utility.triggerCallback(item.callback, {
                              success : true, card : card}
                           );
                           return;
                           break;
                           case UPDATE_CARD_ACTION : item.data.content = data.content;
                           item.data.revision = data.revision;
                           item.data.title = data.title;
                           case UPDATE_MARKERS_ACTION : var card = item.data;
                           resetCardRequest(card);
                           var presentMarkers = card.getMarkers();
                           var remoteMarkers = (data.markers !== undefined) ? data.markers : [];
                           var global = item.action == UPDATE_CARD_ACTION;
                           utility.createShadowedArray(presentMarkers, function(item, index) {
                              card.insertMarkerAt(item, index); }
                           , function(index) {
                              card.removeMarker(presentMarkers[index]); }
                           );
                           utility.syncListsUnordered(presentMarkers, remoteMarkers, syncFunctions.copyPropertiesFuncMarker, syncFunctions.createNewItemFuncMarker, syncFunctions.isSameItemFunc);
                           presentMarkers = remoteMarkers = null;
                           utility.triggerCallback(item.callback, {
                              success : true, card : card}
                           );
                           events["card"].each(function() {
                              this.call(window, {
                                 type : "card", data : card}
                              ); }
                           );
                           return;
                           break;
                           case UPDATE_TRAINING_SESSION_ACTION : break;
                           case LOAD_NOTIFICATIONS_ACTION : notifications = [];
                           data.notifications.each(function() {
                              this.user = getUser(this.userId); delete(this.userId); var notification = new client.data.Notification(); $.extend(notification, this); notifications.push(notification); }
                           );
                           events["notifications"].each(function() {
                              this.call(window, {
                                 type : "notifications", data : notifications.clone()}
                              ); }
                           );
                           break;
                           case MARK_NOTIFICATION_AS_READ_ACTION : item.data.read = true;
                           notifications.remove(item.data);
                           events["notifications"].each(function() {
                              this.call(window, {
                                 type : "notifications", data : notifications.clone()}
                              ); }
                           );
                           break;
                           case GET_CARDS_WITH_MARKER_ACTION : var cards = [];
                           var remoteGroups = [];
                           data.cards.each(function() {
                              var card = getCard(this.id); var group = getGroup(this.group); if(card) {
                                 cards.push(card); }
                              }
                           )utility.triggerCallback(item.callback, {
                              success : true, cards : cards}
                           );
                           return;
                           break;
                           case CHANGE_PASS_ACTION : break;
                           case DELETE_ACCOUNT_ACTION : initialize.call(me, null);
                           break;
                           case LOAD_GROUPS_ACTION : var remoteGroups = data.groups;
                           remoteGroups.each(function() {
                              var group = getGroup(this.id); group.name = this.name ? this.name : "-"; group.category = this.category ? this.category : ""; group.description = this.description ? this.description : ""; group.access = this.access; group.clearMembers(); this.members.each(function() {
                                 var user = getUser(this.user); var member = new client.data.Member(user, this.level); member.enabled = this.enabled; group.addMember(member); }
                              )}
                           )groups.clone().each(function() {
                              var id = this.id; var found = false; remoteGroups.each(function() {
                                 if(this.id == id) {
                                    found = true; return false; }
                                 return true; }
                              ); if(!found) {
                                 groups.remove(this); }
                              }
                           )if(data.userNames !== undefined) {
                              data.userNames.each(function() {
                                 var info = this; var user = getUser(this.id); if(user != null) {
                                    user.name = this.name; if(this.email && this.email.length > 0) {
                                       user.email = this.email; }
                                    }
                                 }
                              )}
                           if(data.externalUsers !== undefined) {
                              data.externalUsers.each(function() {
                                 externalUsers.push( {
                                    id : this.id, name : this.name, email : this.email, mb_id : this.mb_id}
                                 ); }
                              );
                              }
                           utility.triggerCallback(item.callback, {
                              success : true, groups : groups.clone()}
                           );
                           events["groups"].each(function() {
                              this.call(window, {
                                 type : "groups", data : groups.clone()}
                              ); }
                           );
                           return;
                           break;
                           case CREATE_GROUP_ACTION : var remoteGroup = data.group;
                           var group = getGroup(remoteGroup.id);
                           group.name = remoteGroup.name;
                           group.category = remoteGroup.category;
                           group.description = remoteGroup.description;
                           group.access = remoteGroup.access;
                           if(remoteGroup.members !== undefined) {
                              remoteGroup.members.each(function() {
                                 var membership = new client.data.Member(getUser(this.user), this.level); group.addMember(membership); membership.enabled = this.enabled; }
                              )}
                           utility.triggerCallback(item.callback, {
                              success : true, group : group}
                           );
                           events["groups"].each(function() {
                              this.call(window, {
                                 type : "groups", data : groups.clone()}
                              ); }
                           );
                           return;
                           break;
                           case INVITE_TO_GROUP_ACTION : utility.triggerCallback(item.callback, {
                              success : true, notfound : data.notfound, invited : data.invited}
                           );
                           return;
                           break;
                           case JOIN_GROUP_ACTION : var membership;
                           if(data.created === true) {
                              membership = new client.data.Member(currentUser, 0);
                              membership.enabled = data.state;
                              item.data.addMember(membership);
                              }
                           else {
                              membership = currentUser.getMembershipInGroup(item.data);
                              if(membership != null) {
                                 membership.enabled = data.state;
                                 }
                              }
                           utility.triggerCallback(item.callback, {
                              success : true, created : data.created, state : data.state, group : item.data}
                           );
                           events["group"].each(function() {
                              this.call(window, {
                                 type : "group", data : item.data, event : "joined"}
                              ); }
                           );
                           return;
                           break;
                           case UPDATE_MEMBER_ACTION : var group = item.data.group;
                           if(data.level !=- 1) {
                              item.data.level = data.level;
                              item.data.enabled = data.enabled;
                              }
                           else {
                              item.data.group.removeMember(item.data);
                              item.data = null;
                              }
                           utility.triggerCallback(item.callback, {
                              success : true, membership : item.data}
                           );
                           events["group"].each(function() {
                              this.call(window, {
                                 type : "group", data : group}
                              ); }
                           );
                           return;
                           break;
                           case LEAVE_GROUP_ACTION : var group = item.data;
                           var membership = currentUser.getMembershipInGroup(group);
                           group.removeMember(membership);
                           var topicsChanged = false;
                           topics.clone().each(function() {
                              var topic = this; this.getRights().each(function() {
                                 if(this.group == group) {
                                    topicsChanged = true; topic.removeRight(this); }
                                 }
                              ); if(topic.access != client.data.TopicAccessConstants.owner) {
                                 if(topic.getRightCount() == 0) {
                                    topics.remove(topic); }
                                 else {
                                    var access = 0; topic.getRights().each(function() {
                                       access = access | this.access; }
                                    ); topic.access = access; if(access == 0) {
                                       topics.remove(topic); }
                                    }
                                 }
                              }
                           );
                           if(data.state == "groupremoved" || group.access == client.data.GroupAccessConstants.noAccess) {
                              groups.remove(group);
                              events["groups"].each(function() {
                                 this.call(window, {
                                    type : "groups", data : group}
                                 ); }
                              );
                              }
                           else {
                              events["group"].each(function() {
                                 this.call(window, {
                                    type : "group", data : group, event : "left"}
                                 ); }
                              );
                              }
                           if(topicsChanged) {
                              events["topics"].each(function() {
                                 this.call(window, {
                                    type : "topics", data : topics.clone()}
                                 ); }
                              );
                              }
                           break;
                           case UPDATE_GROUP_ACTION : item.data.access = data.access;
                           item.data.description = data.description;
                           item.data.category = data.category;
                           item.data.name = data.name;
                           utility.triggerCallback(item.callback, {
                              success : true, group : item.data}
                           );
                           events["group"].each(function() {
                              this.call(window, {
                                 type : "group", data : item.data}
                              ); }
                           );
                           return;
                           break;
                           case SHARE_TOPIC_ACTION : var right;
                           if(item.data.right == undefined) {
                              right = new client.data.Right(item.data.group);
                              item.data.topic.addRight(right);
                              }
                           else {
                              right = item.data.right;
                              right.topic.addRight(right);
                              }
                           right.id = data.id;
                           right.access = data.mask;
                           utility.triggerCallback(item.callback, {
                              success : true, right : right}
                           );
                           events["topic"].each(function() {
                              this.call(window, {
                                 type : "topic", data : right.topic}
                              ); }
                           );
                           events["group"].each(function() {
                              this.call(window, {
                                 type : "group", data : right.group}
                              ); }
                           );
                           return;
                           break;
                           case INVITE_USER_ACTION : break;
                           case SEARCH_DATABASE_ACTION : utility.triggerCallback(item.callback, {
                              success : true, resultTopics : data.topics, resultGroups : data.groups, resultCards : data.cards}
                           );
                           return;
                           break;
                           case CHECK_EQUATION_SUPPORT_ACTION : case RENDER_EQUATION_ACTION : utility.triggerCallback(item.callback, data);
                           return;
                           break;
                           }
                        utility.triggerCallback(item.callback, {
                           success : true}
                        );
                        }
                     function initialize() {
                        me = this;
                        currentUser = null;
                        markerPlugins = markerPlugins ? markerPlugins : [];
                        displayPlugins = displayPlugins ? displayPlugins : [];
                        topics = [];
                        users = [];
                        groups = [];
                        notifications = [];
                        externalUsers = [];
                        events = {
                           "cards" : [], "card" : [], "topics" : [], "topic" : [], "groups" : [], "group" : [], "notifications" : []};
                        flashCardBoxes = [];
                        if(loader) {
                           loader.empty();
                           }
                        loader = null;
                        for(var i = minLevel; i <= maxLevel; i++) {
                           flashCardBoxes.push(new client.data.FlashCardBox(i));
                           }
                        }
                     initialize.call(this, null);
                     this.destroy = function() {
                        if(loader) {
                           loader.remove();
                           }
                        me = null;
                        markerPlugins = null;
                        displayPlugins = null;
                        topics = null;
                        events = null;
                        flashCardBoxes = null;
                        syncFunctions = null;
                        }
                     }
                  client.Page = function(container, server, header) {
                     var pagePlugins = new Array();
                     this.container = container;
                     this.header = header;
                     this.server = server;
                     this.getLoadedPlugins = function() {
                        return pagePlugins.clone();
                        };
                     this.unloadAll = function() {
                        while(pagePlugins.length > 0) {
                           this.unloadPagePlugin(pagePlugins[0]);
                           }
                        };
                     this.loadPagePlugin = function(plugin) {
                        pagePlugins.push(plugin);
                        plugin.page = this;
                        plugin.load();
                        for(var i = 0; i < pagePlugins.length; i++) {
                           var otherPlugin = pagePlugins[i];
                           if(otherPlugin != plugin) {
                              otherPlugin.onPluginLoad(plugin);
                              plugin.onPluginLoad(otherPlugin);
                              }
                           }
                        };
                     this.unloadPagePlugin = function(plugin) {
                        if(pagePlugins.indexOf(plugin) ==- 1) {
                           return;
                           }
                        for(var i = 0; i < pagePlugins.length; i++) {
                           var otherPlugin = pagePlugins[i];
                           if(otherPlugin != plugin) {
                              otherPlugin.onPluginLoad(plugin);
                              }
                           }
                        plugin.unload();
                        plugin.page = null;
                        pagePlugins.remove(plugin);
                        };
                     }
                  client.Widget = function(args) {
                     }
                  client.Widget.prototype.load = function(args) {
                     return;
                     };
                  client.Widget.prototype.unload = function() {
                     return;
                     };
                  client.Widget.prototype.destroy = function() {
                     return;
                     };
                  client.widgets.Editor = function(args) {
                     var node = args.node;
                     this.document = null;
                     this.window = null;
                     this.body = null;
                     this.head = null;
                     this.frame = null;
                     var keyHook = args.keyHook;
                     var loadedCallback;
                     var content;
                     var isEditor = false;
                     var me = this;
                     var dummyNum = 0;
                     this.load = function(loadArgs) {
                        content = loadArgs.content;
                        var url = config.dummyPath;
                        if(loadArgs.dummyPath !== undefined) {
                           url = loadArgs.dummyPath;
                           }
                        else if($.browser.msie) {
                           url += "?q=" + (dummyNum++);
                           }
                        loadedCallback = loadArgs.callback;
                        isEditor = loadArgs.isEditor === true;
                        if(!this.document) {
                           if(this.frame) {
                              this.frame.remove();
                              }
                           this.frame = $(document.createElement("iframe")).attr( {
                              "src" : url}
                           );
                           if(loadArgs.attributes) {
                              this.frame.attr(loadArgs.attributes);
                              }
                           if(loadArgs.css) {
                              this.frame.css(loadArgs.css);
                              }
                           if($.browser.msie) {
                              this.frame.attr("frameBorder", "0");
                              this.frame.attr("ALLOWTRANSPARENCY", "true");
                              }
                           this.frame.one("load", afterLoad);
                           this.frame.css("border", "0 none").appendTo(node);
                           }
                        else {
                           this.frame.one("load", afterLoad);
                           if(!loadArgs.dummyPath) {
                              this.frame[0].contentWindow.location.reload();
                              }
                           else {
                              this.frame[0].contentWindow.location = url;
                              }
                           }
                        }
                     this.reload = function(url) {
                        this.frame.one("load", afterLoad);
                        this.frame[0].contentWindow.location = url;
                        }
                     this.unload = function() {
                        if(this.frame) {
                           this.frame.remove();
                           this.frame.empty();
                           if(isEditor && this.body) {
                              unbindKeys();
                              }
                           }
                        this.body = this.head = this.frame = this.document = this.window = null;
                        content = null;
                        }
                     this.changeMode = function(editable) {
                        if(isEditor != editable) {
                           isEditor = editable;
                           me.document.designMode = isEditor ? "On" : "Off";
                           if($.browser.msie) {
                              me.frame.one("load", afterLoad);
                              return false;
                              }
                           else {
                              if(isEditor) {
                                 bindKeys();
                                 }
                              else {
                                 unbindKeys();
                                 }
                              }
                           }
                        return true;
                        }
                     function afterLoad(e) {
                        me.window = me.frame[0].contentWindow;
                        me.document = me.window.document;
                        if(isEditor && (!me.document.designMode || me.document.designMode.toLowerCase() != "on")) {
                           try {
                              me.document.designMode = "On";
                              if($.browser.msie) {
                                 me.frame.one("load", afterLoad);
                                 return;
                                 }
                              }
                           catch(ex) {
                              }
                           }
                        me.body = $("body", me.document);
                        if(isEditor) {
                           bindKeys();
                           }
                        if(content)me.body.html(content);
                        content = null;
                        me.head = $("head", me.document);
                        if(loadedCallback) {
                           utility.triggerCallback(loadedCallback);
                           }
                        }
                     function bindKeys() {
                        me.body.bind("mouseup", eventHandler).bind("dragend", eventHandler);
                        $(me.document).bind("keydown", keyDownHandler).bind("keyup", keyUpHandler).bind("keypress", eventHandler);
                        }
                     function unbindKeys() {
                        me.body.unbind("mouseup", eventHandler).unbind("dragend", eventHandler);
                        $(me.document).unbind("keydown", keyDownHandler).unbind("keyup", keyUpHandler).unbind("keypress", eventHandler);
                        }
                     function keyUpHandler(event) {
                        updateToggleButtons();
                        }
                     function keyDownHandler(event) {
                        if(keyHook !== undefined) {
                           if(keyHook.call(this, event) === false) {
                              return;
                              }
                           }
                        var noModifiers =!event.ctrlKey &&!event.altKey &&!event.metaKey;
                        var tabPressed = event.which == 9 && noModifiers;
                        if(tabPressed) {
                           event.preventDefault();
                           execCmd(event.shiftKey ? "outdent" : "indent", null);
                           return;
                           }
                        if(event.which == 67 && event.shiftKey &&!event.altKey && event.metaKey) {
                           event.preventDefault();
                           me.clearFormatting(utility.ClearFormattingConstants.removeStyle | utility.ClearFormattingConstants.removeClass);
                           return;
                           }
                        if(event.which == 109 && event.shiftKey &&!event.altKey && event.metaKey) {
                           event.preventDefault();
                           execCmd("subscript", null);
                           return;
                           }
                        if(event.which == 54 && event.shiftKey &&!event.altKey && event.metaKey) {
                           event.preventDefault();
                           execCmd("superscript", null);
                           return;
                           }
                        if(event.which == 66 &&!event.shiftKey &&!event.altKey && event.metaKey) {
                           event.preventDefault();
                           execCmd("bold", null);
                           updateToggleButtons();
                           return;
                           }
                        if((event.which == 73 || event.which == 69) &&!event.shiftKey &&!event.altKey && event.metaKey) {
                           event.preventDefault();
                           execCmd("italic", null);
                           updateToggleButtons();
                           return;
                           }
                        if(event.which == 85 &&!event.shiftKey &&!event.altKey && event.metaKey) {
                           event.preventDefault();
                           execCmd("underline", null);
                           updateToggleButtons();
                           return;
                           }
                        if(event.which == 80 && event.shiftKey &&!event.altKey && event.metaKey) {
                           event.preventDefault();
                           execCmd("FormatBlock", ($.browser.msie ? "Paragraph" : "p"));
                           return;
                           }
                        if((event.which >= 49 && event.which <= 51) &&!event.altKey && event.metaKey) {
                           event.preventDefault();
                           execCmd("FormatBlock", ($.browser.msie ? "Heading " : "h") + (event.which - 48));
                           return;
                           }
                        if(event.which == 76 && event.shiftKey &&!event.altKey && event.metaKey) {
                           event.preventDefault();
                           execCmd("insertunorderedlist", null);
                           updateToggleButtons();
                           return;
                           }
                        if(event.which == 13 &&!event.altKey &&!event.metaKey &&!event.ctrlKey &&!event.shiftKey) {
                           if(!$.browser.msie) {
                              var selection = me.window.getSelection();
                              if(selection && selection.rangeCount > 0 && selection.isCollapsed) {
                                 var range;
                                 range = selection.getRangeAt(0);
                                 var node = range.endContainer;
                                 if(node.nodeType == 3) {
                                    if(node.nodeValue.length > 2 && (node.nodeValue.substr(0, 2) == "* " || node.nodeValue.substr(0, 2) == "- ")) {
                                       var ele = node;
                                       while((ele = ele.parentNode) && ele.tagName.toLowerCase() != "li" && ele != ele.ownerDocument.body);
                                       if(ele == ele.ownerDocument.body) {
                                          node.nodeValue = node.nodeValue.substr(2);
                                          $(node).wrap(list = $(document.createElement("ul")).append(document.createElement("li")));
                                          range.selectNodeContents(node);
                                          range.collapse(false);
                                          }
                                       return;
                                       }
                                    }
                                 while(node.nodeType != 1 || window.getComputedStyle(node, "").display.toLowerCase() != "block") {
                                    node = node.parentNode;
                                    }
                                 if(node == node.ownerDocument.body || ($.browser.safari && node.tagName == "DIV" && node.parentNode == node.ownerDocument.body)) {
                                    execCmd("FormatBlock", "p");
                                    return;
                                    }
                                 }
                              }
                           }
                        if(event.which == 8 || event.which == 46) {
                           triggerChange();
                           }
                        }
                     function execCmd(cmd, extra) {
                        try {
                           if(me.document.queryCommandEnabled(cmd)) {
                              var needsUndo = false;
                              if($.browser.msie && (cmd == "bold" || cmd == "italic" || cmd == "strikeThrough" || cmd == "underline")) {
                                 var enabled;
                                 try {
                                    needsUndo = me.document.queryCommandState(cmd);
                                    }
                                 catch(ex) {
                                    needsUndo = false;
                                    }
                                 }
                              me.document.execCommand(cmd, false, extra);
                              if(needsUndo) {
                                 undoCmdIE(cmd);
                                 }
                              triggerChange();
                              }
                           }
                        catch(e) {
                           }
                        }
                     function undoCmdIE(cmd) {
                        var selection = $.getSelection( {
                           context : me.document, disableSurrounding : true, returnNullIfCollapsed : true}
                        );
                        if(!selection) {
                           return;
                           }
                        var all;
                        if(selection.is("body")) {
                           all = $("*", selection);
                           }
                        else {
                           all = selection.add($("*", selection));
                           }
                        all.each(function() {
                           var me = $(this); switch(cmd) {
                              case"italic" : if(me.css("font-style") == "italic") {
                                 me.css("font-style", "normal"); }
                              break; case"bold" : if(me.css("font-style") == "bolder") {
                                 $(this).css("font-weight", "normal"); }
                              break; case"strikeThrough" : case"underline" : var deco = me.css("text-decoration").split(" "); var count = deco.length; deco.remove(cmd == "underline" ? "underline" : "line-through"); if(deco.length < count) {
                                 if(deco.length == 0) {
                                    deco.push("none"); }
                                 me.css("text-decoration", deco.join(" ")); }
                              break; }
                           }
                        );
                        updateToggleButtons();
                        }
                     function eventHandler(event) {
                        triggerChange(event);
                        }
                     function triggerChange(event) {
                        if(event !== undefined && event.type == "keypress" && (event.which == 0 || event.ctrlKey || event.metaKey)) {
                           return;
                           }
                        me.body.triggerHandler("change", event);
                        }
                     this.freeze = function() {
                        if(window.getSelection) {
                           this.document.execCommand("contentReadOnly", false, true);
                           }
                        }
                     this.thaw = function() {
                        if(window.getSelection) {
                           this.document.execCommand("contentReadOnly", false, false);
                           }
                        }
                     var toolbarNode;
                     this.setupToolbar = function(args) {
                        toolbarNode = (args && args.node) ? args.node : utility.createToolbar().prependTo(node);
                        var mainitems = [utility.createButton("format-text-bold.png", "bold", lang.BOLD, performAction), utility.createButton("format-text-italic.png", "italic", lang.ITALIC, performAction), utility.createButton("format-text-strikethrough.png", "strikeThrough", lang.STRIKE_THROUGH, performAction), utility.createButton("format-text-underline.png", "underline", lang.UNDERLINE, performAction), utility.createButtonSeparator(), utility.createButton("edit-undo.png", "undo", lang.UNDO, performAction), utility.createButton("edit-redo.png", "redo", lang.REDO, performAction), utility.createButtonSeparator(), utility.createButton("edit-clear.png", "removeFormat", lang.REMOVE_FORMAT, performAction), utility.createButtonSeparator(), utility.createButton("format-list-bullets.png", "insertunorderedlist", lang.INSERTUNORDEREDLIST, performAction), utility.createButton("format-list-numbers.png", "insertorderedlist", lang.INSERTORDEREDLIST, performAction)];
                        toggleButtons = [];
                        toggleButtons.push(mainitems[0], mainitems[1], mainitems[2], mainitems[3], mainitems[10], mainitems[11]);
                        for(var i = 0; i < mainitems.length; i++) {
                           var mainitem = mainitems[i];
                           if(!args || (!args.noUndo || (i != 5 && i != 6 && i != 7))) {
                              mainitem.appendTo(toolbarNode);
                              }
                           if(args && args.additionalHandlers && args.additionalHandlers[i]) {
                              mainitem.bind("click", args.additionalHandlers[i]);
                              }
                           if(args && args.additionalItems && args.additionalItems[i]) {
                              args.additionalItems[i].appendTo(toolbarNode);
                              }
                           }
                        if(!args ||!args.node) {
                           toolbarNode;
                           }
                        }
                     var toggleButtons;
                     function updateToggleButtons() {
                        if(!isEditor ||!toggleButtons) {
                           return;
                           }
                        for(var i = 0; i < toggleButtons.length; i++) {
                           var btn = toggleButtons[i];
                           var enabled;
                           try {
                              enabled = me.document.queryCommandState(btn.action);
                              }
                           catch(ex) {
                              enabled = false;
                              }
                           if(enabled != btn.hasClass("active")) {
                              btn.toggleClass("active");
                              }
                           }
                        }
                     function performAction(e) {
                        var button = e.data;
                        var action = button.action;
                        switch(action) {
                           case"removeFormat" : me.clearFormatting(utility.ClearFormattingConstants.removeStyle | utility.ClearFormattingConstants.removeClass);
                           triggerChange();
                           break;
                           default : execCmd(action, null);
                           updateToggleButtons();
                           break;
                           }
                        }
                     this.clearFormatting = function(level, elements) {
                        var selection = elements !== undefined ? elements : $.getSelection( {
                           context : me.document, disableSurrounding : true, returnNullIfCollapsed : true}
                        );
                        if(!selection) {
                           selection = me.body.children();
                           }
                        utility.clearFormatting(level, selection);
                        updateToggleButtons();
                        }
                     this.isEditor = function() {
                        return isEditor;
                        }
                     this.getContent = function() {
                        if(this.body) {
                           return this.body.html();
                           }
                        else {
                           return null;
                           }
                        }
                     this.getContentText = function() {
                        if(this.body) {
                           return this.body.text();
                           }
                        else {
                           return null;
                           }
                        }
                     this.fixListNesting = function() {
                        $("ul,ol", this.body).each(function() {
                           var parentTag = this.parentNode.tagName.toUpperCase(); if(parentTag == "OL" || parentTag == "UL") {
                              if(this.previousSibling && this.previousSibling.tagName.toUpperCase() == "LI") {
                                 this.previousSibling.appendChild(this); }
                              else {
                                 var cur = $(this); cur.children().insertBefore(this); cur.remove(); }
                              }
                           }
                        );
                        }
                     this.destroy = function() {
                        if(toolbarNode) {
                           toolbarNode.children().remove();
                           }
                        me = null;
                        keyHook = null;
                        }
                     }
                  client.widgets.Editor.prototype = new client.Widget;
                  client.widgets.MapView = function(args) {
                     var isEditor, initialSelection;
                     var hasChanged;
                     var containers;
                     var prereqContainer;
                     var mapViewContainer;
                     function preload() {
                        if(!args.events) {
                           args.events = {
                              };
                           }
                        mapViewContainer = $(document.createElement("div")).addClass("mapView");
                        mapViewContainer.appendTo(args.node);
                        }
                     preload.call(this);
                     var topic, steps, prereqs, access;
                     var titleChanges;
                     var loaded;
                     this.load = function(fargs) {
                        topic = fargs.topic;
                        access = topic.getAccessMask();
                        if(fargs.initialSelection !== undefined) {
                           initialSelection = fargs.initialSelection;
                           }
                        isEditor = fargs.isEditor;
                        titleChanges = {
                           };
                        prereqContainer = $(document.createElement("div")).addClass("mapPrerequisites").appendTo(mapViewContainer);
                        $(document.createElement("h3")).text(lang.PREREQUISITES).appendTo(prereqContainer);
                        if(isEditor) {
                           var prereqCreator = $(document.createElement("a")).attr("href", "javascript:void(0)").addClass("mapPrerequisite").attr("title", lang.ADD_PREREQUISITE);
                           if((access & client.data.TopicAccessConstants.allowRename) == 0) {
                              prereqCreator.hide();
                              }
                           prereqCreator.addClass("create").appendTo(prereqContainer).bind("click", {
                              link : prereqCreator, topic : topic}
                           , prerequisiteHandler);
                           }
                        var anyAdded = false;
                        prereqs = isEditor ? [] : null;
                        topic.getPrerequisites().each(function() {
                           anyAdded = true; if(isEditor) {
                              prereqs.push(this); }
                           showPrerequisite(this, topic); }
                        );
                        prereqContainer.css( {
                           width : 0, display : "none"}
                        );
                        containers = [];
                        for(var i = 0; i < 3; i++) {
                           containers.push($(document.createElement("div")).addClass("mapLevel").appendTo(mapViewContainer));
                           }
                        containers[0].addClass("advanced");
                        containers[2].addClass("illustrative");
                        steps = [];
                        topic.each(function() {
                           var step; step = this.clone(); steps.push(step); showStep(step); }
                        );
                        if(isEditor) {
                           showStep(topic);
                           }
                        containers.each(function() {
                           $(document.createElement("div")).css("clear", "both").appendTo(this); }
                        );
                        hasChanged = false;
                        adjustTopPanel();
                        mapViewContainer.click(hideMenu);
                        $("a.selected", mapViewContainer).each(function() {
                           this.scrollIntoView(false); }
                        );
                        loaded = true;
                        return;
                        }
                     function showPrerequisite(prerequisite, topic) {
                        var prereq = $(document.createElement("a")).attr("href", "javascript:void(0)").addClass("mapPrerequisite").text(prerequisite.title).attr("title", lang.PREREQUISITE);
                        if(prerequisite.topic) {
                           prereq.addClass("available");
                           }
                        if(isEditor) {
                           prereqContainer.children(":last").before(prereq);
                           }
                        else {
                           prereqContainer.append(prereq);
                           }
                        prereq.bind("click", {
                           link : prereq, topic : topic, prereq : prerequisite}
                        , prerequisiteHandler);
                        }
                     function prerequisiteHandler(event) {
                        event.stopPropagation();
                        hideMenu();
                        var link = event.data.link;
                        var prereq = event.data.prereq;
                        menu = prereq ? utility.createMenu() : $(document.createElement("div")).addClass("dialog");
                        menu.bind("click", function(event) {
                           event.stopPropagation()}
                        );
                        menu.link = link;
                        menu.topic = event.data.topic;
                        menu.prereq = prereq;
                        if(!menu.prereq) {
                           menu.append($(document.createElement("h3")).text(lang.PREREQUISITE_ENTER_TITLE));
                           var titleBar = utility.createToolbar();
                           titleBar.appendTo(menu);
                           titleBar.append($(document.createElement("span")).text(lang.TITLE));
                           var field;
                           titleBar.append(field = $(document.createElement("input")));
                           var setTitleButton;
                           titleBar.append(setTitleButton = utility.createMenuItem("dialog-apply.png", null, lang.ADD_PREREQUISITE, null));
                           setTitleButton.bind("click", {
                              field : field}
                           , function(event) {
                              event.stopPropagation(); var field = event.data.field; var text = field.val(); if(text != "" && text != lang.UNTITLED) {
                                 var prereq = new client.data.Topic.Prerequisite(text, null); prereqs.push(prereq); hasChanged = true; showPrerequisite(prereq, menu.topic); hideMenu(); }
                              else {
                                 alert(lang.NO_TITLE_GIVEN); }
                              }
                           );
                           menu.append($(document.createElement("h3")).text(lang.PREREQUISITE_CHOOSE_TOPIC));
                           var topicBar = utility.createToolbar();
                           topicBar.appendTo(menu);
                           var topicSelector;
                           topicBar.append(topicSelector = $(document.createElement("select")));
                           topicSelector.append($(document.createElement("option")).text("").val("")).css("max-width", "300px");
                           var topicsSorted = args.topics.clone();
                           topicsSorted.remove(menu.topic);
                           topicsSorted.sort(function(a, b) {
                              var sortA = a.title; var sortB = b.title; if(sortA == sortB) {
                                 return 0; }
                              return(sortA.toUpperCase() > sortB.toUpperCase()) ? 1 :- 1; }
                           );
                           topicsSorted.each(function() {
                              if(!menu.topic.checkRequired(this)) {
                                 var topicOption = $(document.createElement("option")).val(args.topics.indexOf(this).toString()).text(this.title); topicSelector.append(topicOption); }
                              }
                           );
                           var addTopicButton;
                           topicBar.append(addTopicButton = utility.createMenuItem("dialog-apply.png", null, lang.ADD_PREREQUISITE, null));
                           addTopicButton.bind("click", {
                              selector : topicSelector}
                           , function(event) {
                              event.stopPropagation(); var selector = event.data.selector; if(selector.val() != "") {
                                 var topic = args.topics[selector.val()]; var prereq = new client.data.Topic.Prerequisite(topic.title, topic); prereqs.push(prereq); hasChanged = true; showPrerequisite(prereq, menu.topic); hideMenu(); }
                              else {
                                 alert(lang.NO_TOPIC_SELECTED); }
                              }
                           );
                           }
                        else {
                           if(args.events.onCreatePrerequisiteMenu) {
                              args.events.onCreatePrerequisiteMenu( {
                                 prerequisite : menu.prereq, isEditor : isEditor, menu : menu}
                              );
                              }
                           if(isEditor) {
                              if(menu.children().length > 0) {
                                 menu.append(utility.createMenuItemSeparator());
                                 }
                              if(access & client.data.TopicAccessConstants.allowRename) {
                                 menu.append(utility.createMenuItem("edit-delete.png", "deletePrereq", lang.REMOVE_PREREQUISITE, menuHandler));
                                 }
                              }
                           }
                        showMenu(link);
                        }
                     this.hasChanged = function() {
                        return hasChanged;
                        }
                     this.forceChanged = function() {
                        hasChanged = true;
                        }
                     var containers;
                     function showStep(obj, index) {
                        var isCreate = obj instanceof client.data.Topic;
                        var boxes = [];
                        for(var i = 0; i < 3; i++) {
                           var box;
                           boxes.push(box = $(document.createElement("div")));
                           box.addClass("mapCardBox " + lang.code);
                           switch(i) {
                              case 0 : box.addClass("advanced");
                              break;
                              case 1 : box.addClass("main");
                              break;
                              case 2 : box.addClass("illustrative");
                              break;
                              }
                           if(!isCreate && isEditor) {
                              box.droppable( {
                                 accept : cardAccept, over : function() {
                                    var me = $(this); me.addClass("dragover"); }
                                 , out : function() {
                                    var me = $(this); me.removeClass("dragover"); }
                                 , drop : cardDrop}
                              );
                              }
                           }
                        var element;
                        if(!isCreate) {
                           obj.each(client.data.LevelConstants.advanced, function() {
                              element = createLink(this, client.data.LevelConstants.advanced, obj).appendTo(boxes[0]); }
                           );
                           $(document.createElement("a")).appendTo(boxes[0]);
                           obj.each(client.data.LevelConstants.main, function() {
                              element = createLink(this, client.data.LevelConstants.main, obj).appendTo(boxes[1]); }
                           );
                           obj.each(client.data.LevelConstants.illustrative, function() {
                              element = createLink(this, client.data.LevelConstants.illustrative, obj).appendTo(boxes[2]); }
                           );
                           $(document.createElement("a")).appendTo(boxes[2]);
                           }
                        else {
                           boxes.each(function() {
                              this.addClass("create"); }
                           )$(document.createElement("a")).addClass("mapCreateMain").attr("href", "javascript:void(0)").bind("click", createMainHandler).appendTo(boxes[1]);
                           }
                        for(var i = 0; i < boxes.length; i++) {
                           var before = (index >- 1) ? containers[i][0].childNodes[index] : null;
                           containers[i][0].insertBefore(boxes[i][0], before ? before : null);
                           }
                        }
                     function cardAccept(droppable) {
                        var box = $(this);
                        var parent = droppable.parent();
                        if(parent.index(box) !=- 1) {
                           return false;
                           }
                        if(!droppable.hasClass("new") && (access & client.data.TopicAccessConstants.allowRearrangingCards) == 0) {
                           return false;
                           }
                        if(box.hasClass("advanced") && box.children(".mapCard").length >= config.CARD_LIMITS[client.data.LevelConstants.advanced]) {
                           return false;
                           }
                        if(box.hasClass("illustrative") && box.children(".mapCard").length >= config.CARD_LIMITS[client.data.LevelConstants.illustrative]) {
                           return false;
                           }
                        if(parent.hasClass("main")) {
                           if(box.hasClass("main")) {
                              return(access & client.data.TopicAccessConstants.allowRearrangingCards) != 0;
                              }
                           else {
                              var dragIndex = parent.parent().children().index(parent);
                              var dropIndex = box.parent().children().index(box);
                              var upper = containers[0].children().eq(dragIndex);
                              var lower = containers[2].children().eq(dragIndex);
                              var upperLength = upper.children(".mapCard").length;
                              var lowerLength = lower.children(".mapCard").length;
                              if(upperLength != 0 || lowerLength != 0) {
                                 return false;
                                 }
                              if(dragIndex == dropIndex) {
                                 if(dropIndex > 0) {
                                    box = containers[0].children().eq(dropIndex - 1);
                                    if(box.hasClass("advanced") && box.children(".mapCard").length >= config.CARD_LIMITS[client.data.LevelConstants.advanced]) {
                                       return false;
                                       }
                                    if(box.hasClass("illustrative") && box.children(".mapCard").length >= config.CARD_LIMITS[client.data.LevelConstants.illustrative]) {
                                       return false;
                                       }
                                    return true;
                                    }
                                 else {
                                    return false;
                                    }
                                 }
                              else {
                                 return true;
                                 }
                              }
                           }
                        else {
                           return!box.hasClass("main") || steps.length < config.CARD_LIMITS[0];
                           }
                        }
                     function cardDrop(e, ui) {
                        var me = $(this);
                        me.removeClass("dragover");
                        var dragParent = ui.draggable.parent();
                        var from = dragParent.parent().children().index(dragParent);
                        var to = me.parent().children().index(me);
                        if(dragParent.hasClass("main")) {
                           if(me.hasClass("main")) {
                              moveStep(from, from > to ? to + 1 : to);
                              }
                           else {
                              if(from == to) {
                                 to--;
                                 me = me.prev();
                                 }
                              me.append(ui.draggable);
                              var destination = steps[to];
                              removeStep(currentTag.step);
                              currentTag.step.removeCard(currentTag.card);
                              currentTag.step = destination;
                              var level;
                              if(me.hasClass("advanced")) {
                                 level = client.data.LevelConstants.advanced;
                                 }
                              else {
                                 level = client.data.LevelConstants.illustrative;
                                 }
                              destination.addCard(level, currentTag.card);
                              hasChanged = true;
                              }
                           }
                        else {
                           if(me.hasClass("main")) {
                              currentTag.step.removeCard(currentTag.card);
                              insertStep(topic, to + 1, currentTag.card);
                              ui.draggable.remove();
                              hasChanged = true;
                              }
                           else {
                              var destination = steps[to];
                              currentTag.step.removeCard(currentTag.card);
                              currentTag.step = destination;
                              var level;
                              if(me.hasClass("advanced")) {
                                 level = client.data.LevelConstants.advanced;
                                 }
                              else {
                                 level = client.data.LevelConstants.illustrative;
                                 }
                              destination.addCard(level, currentTag.card);
                              me.append(ui.draggable);
                              hasChanged = true;
                              }
                           }
                        adjustTopPanel();
                        }
                     var currentTag;
                     function createLink(obj, position, step) {
                        var element = $(document.createElement("a")).attr("href", "javascript:void(0)").addClass("mapCard");
                        if(loaded) {
                           element.addClass("new");
                           }
                        var tag = {
                           card : obj, step : step, link : element};
                        element.bind("click", tag, showMenuHandler).bind("contextmenu", tag, showMenuHandler);
                        element.append(document.createElement("span"));
                        if(isEditor) {
                           var options = {
                              delay : 300, revert : true, scroll : true, start : function(e, ui) {
                                 hideMenu();
                                 $(this).unbind("click");
                                 currentTag = arguments.callee.tag;
                                 }
                              , stop : function() {
                                 $(this).bind("click", currentTag, showMenuHandler);
                                 currentTag = undefined;
                                 }
                              };
                           options.start.tag = tag;
                           element.draggable(options);
                           }
                        updateTitle(element, obj);
                        if(obj.hidden == true) {
                           element.addClass("hidden");
                           }
                        if(obj.content == null &&!obj.needsLoading) {
                           element.addClass("empty");
                           }
                        if(obj == initialSelection) {
                           element.addClass("selected");
                           }
                        return element;
                        }
                     function getTitle(card, abbr) {
                        var title = (card.id == null || titleChanges[card.id] === undefined) ? card.title : titleChanges[card.id];
                        var maxLength = 20;
                        if(abbr && title.length > maxLength) {
                           title = title.substr(0, maxLength - 3) + "...";
                           }
                        return title;
                        }
                     function updateTitle(element, card) {
                        element.attr("title", getTitle(card)).children("span").text(getTitle(card));
                        }
                     function adjustTopPanel() {
                        var cardBoxes = containers[0].children(".mapCardBox");
                        var levelOffset = prereqContainer.width();
                        containers.each(function() {
                           this.css("margin-left", levelOffset + "px")}
                        );
                        var totalWidth = 0;
                        cardBoxes.each(function() {
                           $(this).css("padding-top", 0); totalWidth += this.offsetWidth; }
                        );
                        containers.each(function() {
                           this.css("width", totalWidth); }
                        );
                        var containerHeight = (containers[0])[0].offsetHeight;
                        cardBoxes.each(function() {
                           var height = this.offsetHeight; if(height < containerHeight) {
                              $(this).css("padding-top", containerHeight - height); }
                           }
                        );
                        }
                     var menu;
                     function showMenuHandler(event) {
                        event.stopPropagation();
                        event.preventDefault();
                        var card = event.data.card;
                        var step = event.data.step;
                        var link = event.data.link;
                        var index = steps.indexOf(step);
                        hideMenu();
                        menu = utility.createMenu();
                        menu.card = card;
                        menu.step = step;
                        menu.link = link;
                        menu.index = index;
                        if(args.events.onCreateMenu) {
                           args.events.onCreateMenu( {
                              step : card.id != null ? card.step : null, card : card, topic : topic, menu : menu, isEditor : isEditor}
                           );
                           }
                        var level = step.getLevelOfCard(card);
                        if(isEditor) {
                           if(access & client.data.TopicAccessConstants.allowEditingContent) {
                              utility.createMenuItem("rename.png", "rename", lang.RENAME + " \"" + getTitle(card, true) + "\"", menuHandler).appendTo(menu);
                              }
                           if(access & client.data.TopicAccessConstants.allowRemovingCards) {
                              utility.createMenuItemSeparator().appendTo(menu);
                              utility.createMenuItem("edit-delete.png", "remove", lang.DELETE, menuHandler).appendTo(menu);
                              }
                           if(level == client.data.LevelConstants.main) {
                              if(access & client.data.TopicAccessConstants.allowRearrangingCards) {
                                 utility.createMenuItemSeparator().appendTo(menu);
                                 utility.createMenuItem("go-next-create.png", "addright", lang.ADD_RIGHT, menuHandler).appendTo(menu);
                                 utility.createMenuItem("go-previous-create.png", "addleft", lang.ADD_LEFT, menuHandler).appendTo(menu);
                                 var stepIndex = steps.indexOf(step);
                                 if(stepIndex != steps.length - 1) {
                                    utility.createMenuItem("go-next.png", "moveright", lang.MOVE_RIGHT, menuHandler).appendTo(menu);
                                    }
                                 if(stepIndex != 0) {
                                    utility.createMenuItem("go-previous.png", "moveleft", lang.MOVE_LEFT, menuHandler).appendTo(menu);
                                    }
                                 }
                              }
                           }
                        showMenu(link);
                        }
                     function showMenu(link) {
                        var pos = utility.getRelativePosition(link.get(0), args.node[0]);
                        pos.top += link.height() + 10;
                        pos.left += 2;
                        menu.css(pos);
                        menu.appendTo(args.node);
                        menu.focusFirstEditable();
                        }
                     function hideMenu() {
                        if(menu) {
                           menu.remove();
                           menu = null;
                           }
                        }
                     function menuHandler(event) {
                        event.stopPropagation();
                        var button = event.data;
                        var card = menu.card;
                        var step = menu.step;
                        var link = menu.link;
                        var index = menu.index;
                        if(button.action == "deletePrereq") {
                           link.remove();
                           prereqs.remove(menu.prereq);
                           hasChanged = true;
                           }
                        else if(button.action == "remove") {
                           var level = step.getLevelOfCard(card);
                           if(canDelete(card)) {
                              if(level == client.data.LevelConstants.advanced || level == client.data.LevelConstants.illustrative) {
                                 step.removeCard(card);
                                 hasChanged = true;
                                 link.remove();
                                 adjustTopPanel();
                                 }
                              else {
                                 removeStep(step);
                                 hasChanged = true;
                                 }
                              triggerDelete(card);
                              }
                           }
                        else if(button.action == "rename") {
                           var title = getTitle(card);
                           var newTitle = prompt(lang.ENTER_NEW_CARD_TITLE, title);
                           if(newTitle && newTitle.length > 0 && newTitle != title) {
                              if(card.id == null) {
                                 card.title = newTitle;
                                 }
                              else {
                                 titleChanges[card.id] = newTitle;
                                 }
                              updateTitle(link, card);
                              hasChanged = true;
                              }
                           }
                        else if(button.action == "addleft" || button.action == "addright") {
                           insertStep(topic, index + (button.action == "addleft" ? 0 : 1));
                           }
                        else if(button.action == "moveleft" || button.action == "moveright") {
                           var index = steps.indexOf(step);
                           var destIndex = index;
                           if(button.action == "moveleft") {
                              destIndex--;
                              }
                           else {
                              destIndex++;
                              }
                           moveStep(index, destIndex);
                           }
                        hideMenu();
                        }
                     function moveStep(from, to) {
                        containers.each(function() {
                           var current = this.children().eq(from); if(to == 0) {
                              this.children().eq(0).before(current); }
                           else {
                              this.children().eq(to > from ? to : to - 1).after(current); }
                           }
                        );
                        var step = steps[from];
                        steps.splice(from, 1);
                        steps.splice(to, 0, step);
                        hasChanged = true;
                        }
                     function createMainHandler(event) {
                        if(steps.length < config.CARD_LIMITS[0]) {
                           insertStep(topic, steps.length);
                           event.target.scrollIntoView(false);
                           }
                        else {
                           alert(lang.TOO_MANY_STEPS);
                           }
                        }
                     function insertStep(topic, index, card) {
                        var step = new client.data.Step();
                        var newCard;
                        if(card) {
                           newCard = card;
                           }
                        else {
                           newCard = new client.data.Card();
                           }
                        step.addCard(client.data.LevelConstants.main, newCard);
                        steps.splice(index, 0, step);
                        showStep(step, index);
                        adjustTopPanel();
                        if(args.events.onCreation) {
                           args.events.onCreation( {
                              step : step}
                           );
                           }
                        hasChanged = true;
                        }
                     function removeStep(step) {
                        var index = steps.indexOf(step);
                        steps.remove(step);
                        containers.each(function() {
                           this.children().eq(index).remove(); }
                        );
                        adjustTopPanel();
                        hasChanged = true;
                        }
                     function insertCard(step, level) {
                        var index = steps.indexOf(step);
                        step.addCard(level, new client.data.Card());
                        showStep(step, index);
                        adjustTopPanel();
                        hasChanged = true;
                        }
                     this.getChanges = function() {
                        var obj = {
                           };
                        if(hasChanged) {
                           obj.prerequisites = [];
                           prereqs.each(function() {
                              var prereq = {
                                 title : this.title}; if(this.topic) {
                                 prereq.topic = this.topic.id; }
                              obj.prerequisites.push(prereq); }
                           );
                           obj.cards = [];
                           for(var index = 0; index < steps.length; index++) {
                              steps[index].transformStatic().each(function() {
                                 this.index = index; if(this.id !== undefined && titleChanges[this.id] !== undefined) {
                                    this.title = titleChanges[this.id]; }
                                 obj.cards.push(this); }
                              );
                              }
                           }
                        return obj;
                        }
                     this.cleanup = function() {
                        if(access & client.data.TopicAccessConstants.allowRemovingCards) {
                           steps.clone().each(function() {
                              var step = this; var cards = step.getCards(); cards.remove(step.getMainCard()); cards.push(step.getMainCard()); var refreshStep = false; cards.each(function() {
                                 if(this.needsLoading == false && this.content == null && this.request == null) {
                                    if(step.getLevelOfCard(this) != client.data.LevelConstants.main) {
                                       if(canDelete(this, true)) {
                                          step.removeCard(this); triggerDelete(this); refreshStep = true; }
                                       }
                                    else if(step.getCardCount() == 1) {
                                       if(canDelete(this, true)) {
                                          removeStep(step); triggerDelete(this); refreshStep = false; }
                                       return false; }
                                    }
                                 }
                              ); if(refreshStep) {
                                 var index = steps.indexOf(step); containers.each(function() {
                                    this.children().eq(index).remove(); }
                                 ); showStep(step, index); hasChanged = true; }
                              }
                           );
                           adjustTopPanel();
                           }
                        }
                     function canDelete(card, cleanup) {
                        var cancelArgs = utility.createCancelArgs();
                        if(args.events.onDeleting) {
                           cancelArgs.isLast = (steps.length == 1 && steps[0].getCardCount() == 1);
                           if(cleanup) {
                              cancelArgs.isCleanup = true;
                              }
                           cancelArgs.card = card;
                           args.events.onDeleting(cancelArgs);
                           }
                        return!utility.wasCancelled(cancelArgs);
                        }
                     function triggerDelete(card) {
                        if(args.events.onDelete) {
                           args.events.onDelete( {
                              card : card}
                           );
                           }
                        }
                     this.unload = function() {
                        if(loaded) {
                           mapViewContainer.empty();
                           hideMenu();
                           topic = null;
                           loaded = false;
                           }
                        }
                     this.destroy = function() {
                        steps = null;
                        prereqs = null;
                        mapViewContainer.remove();
                        mapViewContainer = null;
                        }
                     }
                  client.widgets.MapView.createMapView = function(topic, isEditor, container, pos, topics, eventHandlers, label, initialSelection, noLabels) {
                     var cont = $(document.createElement("div"))if(pos !== null) {
                        cont.addClass("dialog").css("width", 550).css(pos);
                        }
                     cont.appendTo(container);
                     cont.css("position", "relative");
                     cont.click(function(event) {
                        event.stopPropagation(); }
                     );
                     if(label) {
                        $(document.createElement("p")).text(label).appendTo(cont);
                        }
                     var toolbar = utility.createToolbar().appendTo(cont);
                     var map = new client.widgets.MapView( {
                        node : cont, events : eventHandlers, topics : topics}
                     );
                     map.load( {
                        topic : topic, isEditor : isEditor, initialSelection : initialSelection}
                     );
                     var mapObj = {
                        changes : {
                           }
                        };
                     var renameable = isEditor && (topic.access & client.data.TopicAccessConstants.allowRename);
                     $(document.createElement("span")).text(lang.TITLE).appendTo(toolbar);
                     getEditor(topic.title, "title", mapObj, renameable &&!noLabels).appendTo(toolbar);
                     $(document.createElement("span")).text(lang.CATEGORY).appendTo(toolbar);
                     getEditor(topic.category, "category", mapObj, renameable &&!noLabels).appendTo(toolbar);
                     if(!initialSelection) {
                        cont.focusFirstEditable();
                        }
                     else {
                        $("a.selected", cont).focusFirstEditable();
                        }
                     mapObj.map = map;
                     mapObj.container = cont;
                     mapObj.toolbar = toolbar;
                     mapObj.topic = topic;
                     mapObj.isEditor = isEditor;
                     return mapObj;
                     function getEditor(text, name, store, editable) {
                        if(editable) {
                           var handler = function(cancelArgs) {
                              if(cancelArgs.text == "") {
                                 utility.cancelEvent(cancelArgs);
                                 return;
                                 }
                              var self = arguments.callee;
                              if(self.text != cancelArgs.text) {
                                 self.store.changes[self.name] = cancelArgs.text;
                                 self.store.changed = true;
                                 }
                              };
                           handler.store = store;
                           handler.name = name;
                           handler.text = text;
                           return $.createEditableLabel(null, utility.createCallback(this, handler)).text(text).addClass("field-" + name);
                           }
                        else {
                           return $(document.createElement("span")).css("font-weight", "bold").text(text).appendTo(toolbar);
                           }
                        }
                     }
                  client.widgets.MapView.destroyMap = function(map) {
                     map.map.unload();
                     map.map.destroy();
                     map.container.remove();
                     map.container.empty();
                     map.toolbar.empty();
                     map.toolbar.remove();
                     map = null;
                     };
                  client.widgets.MapView.prototype = new client.Widget;
                  client.widgets.TreeView = function(args) {
                     this.generalOffset = args.generalOffset ? generalOffset : 0;
                     var columns = args.columns;
                     var container = $(document.createElement("table")).attr( {
                        cellspacing : 0, cellpadding : 0}
                     ).addClass("treeview").css("width", "100%").appendTo(args.container);
                     this.getContainer = function() {
                        return args.container;
                        }
                     var showCheckBoxes = args.showCheckBoxes;
                     var newItemCallback = args.newItemCallback;
                     var noSubAdd = args.noSubAdd == true;
                     var multiSelect = args.multiSelect == true;
                     var indentation;
                     this.load = function(args) {
                        var items = args.items;
                        indentation = 18;
                        var columnHeaders = addColumns.call(this, columns);
                        if(items.length > 0) {
                           var mainLevel = addLevel(items, 1, null);
                           mainLevel.each(function() {
                              this.css("display", ""); }
                           )}
                        else {
                           $(document.createElement("tr")).addClass("row").append($(document.createElement("td")).text((args.emptyRowLabel !== undefined) ? args.emptyRowLabel : lang.EMPTY_ROW).addClass("column").attr("colspan", columns.length)).appendTo(container);
                           }
                        }
                     function addColumns(columns) {
                        var columnHeaders = new Array();
                        var row = $(document.createElement("tr")).addClass("row").addClass("header");
                        for(var i = 0; i < columns.length; i++) {
                           var columnHeader = $(document.createElement("td")).addClass("column").text(columns[i]);
                           row.append(columnHeader);
                           columnHeaders.push(columnHeader);
                           }
                        container.append(row);
                        return columnHeaders;
                        }
                     function addLevel(items, level, parent, before) {
                        var rows = new Array();
                        if(items) {
                           for(var i = 0; i < items.length; i++) {
                              rows.push(addItem(items[i], level, parent, before));
                              }
                           }
                        if(newItemCallback &&!(noSubAdd && level > 1)) {
                           var addTopicLabel = $.createEditableLabel(utility.createCallback(this, beforeLabelEdit), utility.createCallback(this, afterLabelEdit));
                           addTopicLabel.tag = {
                              items : items, parent : parent, level : level};
                           addTopicLabel.text(args.ghostLabel ? args.ghostLabel : lang.GHOST_ENTRY);
                           var addTopicCell = $(document.createElement("td")).addClass("column").attr( {
                              colspan : columns.length}
                           ).append(addTopicLabel).css("padding-left", (indentation * (level - 1)) + "px");
                           var row = $(document.createElement("tr")).addClass("row").css("display", "none").append(addTopicCell).appendTo(container);
                           if(before) {
                              row.insertBefore(before);
                              }
                           rows.push(row);
                           }
                        return rows;
                        }
                     function addItem(item, level, parent, before) {
                        var row = $(document.createElement("tr")).addClass("row").css("display", "none");
                        if(before) {
                           row.insertBefore(before);
                           }
                        else {
                           row.appendTo(container);
                           }
                        var increaseMargin = 0;
                        var rowObj = new Object();
                        var firstCell;
                        firstCell = $(document.createElement("td")).addClass("column").css( {
                           "padding-left" : (indentation * (level - 1)) + "px"}
                        );
                        firstCell.appendTo(row);
                        var expander;
                        if((item.children && item.children.length > 0) || (newItemCallback &&!noSubAdd)) {
                           expander = $(document.createElement("a")).attr("href", "javascript:void(0)").addClass("pointer").addClass("expander").bind("click", rowObj, expanderClick);
                           expander.appendTo(firstCell);
                           }
                        var checkBox;
                        if(showCheckBoxes &&!item.noCheckBox) {
                           var span = $(document.createElement("span")).addClass("placeHolder").appendTo(firstCell);
                           checkBox = $("<input type=\"" + (multiSelect ? "checkbox" : "radio") + "\" name=\"" + (multiSelect ? "" : "treeview") + "\"/>");
                           checkBox.addClass("checkbox").appendTo(span);
                           if($.browser.msie) {
                              checkBox.css("margin-left", "-4px");
                              }
                           }
                        if(item.icon) {
                           $(document.createElement("span")).addClass("placeHolder").css( {
                              background : "url(" + config.imagePath + item.icon + ") no-repeat"}
                           ).appendTo(firstCell);
                           }
                        firstCell.append(typeof item.title == "string" ? $(document.createTextNode(item.title)) : item.title);
                        for(var i = 0; i < item.subItems.length; i++) {
                           var cell = $(document.createElement("td")).addClass("column").append(typeof item.subItems[i] == "string" ? $(document.createTextNode(item.subItems[i])) : item.subItems[i]).appendTo(row);
                           }
                        if(item.children || newItemCallback) {
                           var items = addLevel(item.children, level + 1, item, before);
                           rowObj.subItems = items;
                           }
                        rowObj.row = row;
                        rowObj.item = item;
                        rowObj.checkBox = checkBox;
                        rowObj.expander = expander;
                        rowObj.parent = parent;
                        item.row = rowObj;
                        return row;
                        }
                     function expanderClick(event) {
                        event.stopPropagation();
                        expandItem(event.data.item, event.data.expander.hasClass("expanded"), false);
                        }
                     this.showItem = function(item, recursive) {
                        var parent = item;
                        if(recursive) {
                           expandItem(parent, false, true);
                           }
                        while((parent = parent.row.parent)) {
                           expandItem(parent, false, false);
                           }
                        }
                     this.removeItem = function(item) {
                        if(item.row.parent != null) {
                           item.row.parent.children.remove(item);
                           }
                        item.row.row.remove();
                        for(var j = 0; j < item.children.length; j++) {
                           this.removeItem(item.children[j].row.item);
                           }
                        }
                     function expandItem(item, collapse, recursive) {
                        if(item.row.expander && item.row.expander.hasClass("expander")) {
                           $.fn[collapse ? "removeClass" : "addClass"].call(item.row.expander, "expanded");
                           for(var i = 0; i < item.row.subItems.length; i++) {
                              item.row.subItems[i].css("display", collapse ? "none" : "");
                              }
                           if(collapse || recursive) {
                              for(var j = 0; j < item.children.length; j++) {
                                 expandItem(item.children[j].row.item, collapse, recursive);
                                 }
                              }
                           }
                        }
                     var sortedBy;
                     function columnClick(event) {
                        var column = event.data;
                        if(column.hasClass("headerAscending")) {
                           column.removeClass("headerAscending").addClass("headerDescending");
                           }
                        else if(column.hasClass("headerDescending")) {
                           column.removeClass("headerDescending");
                           column = null;
                           }
                        else {
                           column.addClass("headerAscending");
                           }
                        sortedBy = column;
                        }
                     function beforeLabelEdit(args) {
                        var text = args.text;
                        if(text == lang.GHOST_TOPIC) {
                           args.text = "";
                           }
                        }
                     function afterLabelEdit(args) {
                        var ghostData = args.box.tag;
                        var newItem = utility.triggerCallback(newItemCallback, {
                           parent : ghostData.parent, text : args.text}
                        );
                        if(newItem) {
                           var newRow = addItem(newItem, ghostData.level, ghostData.parent, args.editor.parent().parent()).css("display", "");
                           if(ghostData.parent)ghostData.parent.subItems.push(newRow);
                           }
                        }
                     this.unload = function() {
                        container.children().remove();
                        }
                     this.destroy = function() {
                        container = null;
                        newItemCallback = null;
                        }
                     }
                  client.widgets.TreeView.prototype = new client.Widget;
                  client.widgets.TreeViewItem = function(title, subItems, children, icon, noCheckBox) {
                     this.title = title;
                     this.icon = icon ? icon : null;
                     this.subItems = subItems;
                     this.children = children;
                     this.noCheckBox = noCheckBox == true;
                     this.isChecked = function() {
                        if(this.row.checkBox === undefined) {
                           return false;
                           }
                        return this.row.checkBox.attr("checked");
                        }
                     this.row = null;
                     }
                  client.widgets.ProgressBox = function(args) {
                     var isEditor = args.isEditor;
                     var maincont;
                     this.getContainer = function() {
                        return maincont;
                        }
                     this.getTopics = function() {
                        return args.topics.clone();
                        }
                     var items;
                     function preload() {
                        var node = args.node;
                        var topics = args.topics;
                        maincont = $(document.createElement("div")).addClass("progressboxcontainer");
                        items = [];
                        var totalItemCount = 0;
                        topics.each(function() {
                           this.each(function() {
                              totalItemCount++; items.push(showStep(this, maincont, null)); }
                           )}
                        );
                        var additionalWidth = 0;
                        if(args.additionalWidth !== undefined) {
                           additionalWidth = args.additionalWidth;
                           }
                        maincont.css( {
                           "width" : totalItemCount * 28 + additionalWidth}
                        );
                        maincont.appendTo(node);
                        }
                     preload.call(this);
                     var activeStep;
                     this.load = function(fargs) {
                        items.each(function() {
                           if(this.step == fargs.selectedCard.step) {
                              updateStep(activeStep = this, fargs.selectedCard); return false; }
                           return true; }
                        );
                        }
                     function showStep(step, node, selectedCard) {
                        var stepNode = $(document.createElement("div")).css( {
                           width : 28, height : 68, "float" : "left"}
                        );
                        var upperIcon = $(document.createElement("img")).css( {
                           width : 20, height : 20, "padding-right" : 8, "padding-bottom" : 4}
                        ).appendTo(stepNode);
                        if(step.getCardCount(client.data.LevelConstants.advanced) > 0) {
                           upperIcon.bind("click", {
                              step : step, level : client.data.LevelConstants.advanced}
                           , linkHandler).css("cursor", "pointer");
                           }
                        var mainIcon = $(document.createElement("img")).css( {
                           width : 24, height : 20, "padding-right" : 4}
                        ).bind("click", {
                           step : step, level : client.data.LevelConstants.main}
                        , linkHandler).attr("title", step.getMainCard().getTitle()).css("cursor", "pointer").appendTo(stepNode);
                        var lowerIcon = $(document.createElement("img")).css( {
                           width : 20, height : 20, "padding-right" : 8, "padding-top" : 4}
                        ).css("cursor", "pointer").appendTo(stepNode);
                        if(step.getCardCount(client.data.LevelConstants.illustrative) > 0) {
                           lowerIcon.bind("click", {
                              step : step, level : client.data.LevelConstants.illustrative}
                           , linkHandler).css("cursor", "pointer");
                           }
                        stepNode.appendTo(node);
                        var stepInfo = {
                           step : step, upperIcon : upperIcon, mainIcon : mainIcon, lowerIcon : lowerIcon};
                        updateStep(stepInfo);
                        return stepInfo;
                        }
                     function updateStep(stepInfo, selectedCard) {
                        if(!stepInfo.step.topic) {
                           return;
                           }
                        var hasUpper = false;
                        var completedUpper = 0;
                        var activeUpper = false;
                        var hasLower = false;
                        var completedLower = 0;
                        var activeLower = false;
                        var step = stepInfo.step;
                        step.each(client.data.LevelConstants.advanced, function() {
                           hasUpper = true; if(isCompleted(this)) {
                              completedUpper++; }
                           if(this == selectedCard) {
                              activeUpper = true; }
                           }
                        );
                        var completedMain = isCompleted(step.getMainCard());
                        var activeMain = step.getMainCard() == selectedCard;
                        step.each(client.data.LevelConstants.illustrative, function() {
                           hasLower = true; if(isCompleted(this)) {
                              completedLower++; }
                           if(this == selectedCard) {
                              activeLower = true; }
                           }
                        );
                        var mainPos = 0;
                        if(step.topic.getIndexOfStep(step) == 0) {
                           mainPos = 1;
                           }
                        else if(step.topic.getIndexOfStep(step) == step.topic.getStepCount() - 1) {
                           mainPos = 2;
                           }
                        if(hasUpper) {
                           stepInfo.upperIcon.attr("title", getFirstUnviewed(client.data.LevelConstants.advanced, step).getTitle());
                           }
                        if(hasLower) {
                           stepInfo.lowerIcon.attr("title", getFirstUnviewed(client.data.LevelConstants.illustrative, step).getTitle());
                           }
                        var upperIconPath = getIcon(client.data.LevelConstants.advanced, hasUpper, activeUpper, completedUpper, step.getCardCount(client.data.LevelConstants.advanced), 0);
                        var mainIconPath = getIcon(client.data.LevelConstants.main, true, activeMain, completedMain ? 1 : 0, 1, mainPos);
                        var lowerIconPath = getIcon(client.data.LevelConstants.illustrative, hasLower, activeLower, completedLower, step.getCardCount(client.data.LevelConstants.illustrative), 0);
                        stepInfo.upperIcon.attr("src", upperIconPath);
                        stepInfo.mainIcon.attr("src", mainIconPath);
                        stepInfo.lowerIcon.attr("src", lowerIconPath);
                        return;
                        function isCompleted(card) {
                           var editable = card.step.topic.isEditable();
                           return(!editable && card.studied) || (editable && (card.edited || card.needsLoading || card.content != null))}
                        function getIcon(type, show, active, completed, count, pos) {
                           var typeString = "progress";
                           if(type == client.data.LevelConstants.advanced) {
                              typeString += "-advanced";
                              }
                           else if(type == client.data.LevelConstants.main) {
                              typeString += "-main";
                              }
                           else if(type == client.data.LevelConstants.illustrative) {
                              typeString += "-illustrative";
                              }
                           var path;
                           if(show) {
                              path = config.imagePath + typeString;
                              if(active) {
                                 path += "-active.png";
                                 }
                              else {
                                 if(completed == 0) {
                                    path += "-none.png";
                                    }
                                 else {
                                    if(completed == count) {
                                       path += "-visited.png";
                                       }
                                    else {
                                       path += "-half.png";
                                       }
                                    }
                                 }
                              }
                           else {
                              path = config.imagePath + "pixel.gif";
                              }
                           return path;
                           }
                        }
                     function linkHandler(event) {
                        event.stopPropagation();
                        if(args.openCardCallback) {
                           var step = event.data.step;
                           var level = event.data.level;
                           var card = null;
                           card = getFirstUnviewed(level, step);
                           if(card != null) {
                              utility.triggerCallback(args.openCardCallback, card);
                              }
                           }
                        }
                     function getFirstUnviewed(level, step) {
                        var card;
                        step.each(level, function() {
                           card = this; if((!isEditor &&!this.studied) || (isEditor && (!card.edited &&!card.needsLoading && card.content == null))) {
                              return false; }
                           return true; }
                        );
                        return card;
                        }
                     this.unload = function() {
                        if(activeStep) {
                           updateStep(activeStep, null);
                           }
                        }
                     this.destroy = function() {
                        if(maincont) {
                           maincont.remove();
                           maincont.empty();
                           maincont = null;
                           }
                        items = null;
                        }
                     }
                  client.widgets.ProgressBox.prototype = new client.Widget;
                  client.widgets.FilterSearch = function(args) {
                     setupUI();
                     this.load = function(fargs) {
                        if(fargs !== undefined && fargs.term !== undefined) {
                           this.search(fargs.term);
                           }
                        else {
                           updateUI();
                           }
                        }
                     this.getContainer = function() {
                        return args.container;
                        }
                     var loader;
                     var resultsPanel;
                     var searchField;
                     function setupUI() {
                        resultsPanel = $(document.createElement("div")).addClass("results");
                        loader = $(document.createElement("div")).addClass("loader").appendTo(resultsPanel);
                        searchField = utility.createInput(lang.SEARCH).attr("accesskey", "f").addClass("search").bind("keypress", keypressHandler).appendTo(resultsPanel);
                        if(args.introHTML !== undefined) {
                           resultsPanel.append($(document.createElement("div")).addClass("result").html(args.introHTML));
                           }
                        resultsPanel.appendTo(args.container);
                        }
                     var items;
                     this.getItems = function() {
                        return items ? items.clone() : [];
                        }
                     this.getCheckedItems = function() {
                        var list = [];
                        if(items) {
                           items.each(function() {
                              if(this.checked) {
                                 list.push(this); }
                              }
                           )}
                        return list;
                        }
                     this.removeItem = function(item) {
                        items.remove(item);
                        updateUI();
                        }
                     function updateUI() {
                        $("div.result", resultsPanel).remove();
                        if(items !== undefined && items instanceof Array && items.length > 0) {
                           items.sort(function(a, b) {
                              if(a.order !== undefined && b.order !== undefined) {
                                 return a.order - b.order; }
                              return 0; }
                           );
                           var even = true;
                           items.each(function() {
                              even =!even; var result = $(document.createElement("div")).addClass("result"); if(even) {
                                 result.addClass("even"); }
                              if(this.highlight) {
                                 result.addClass("highlight"); }
                              if(this.provider !== undefined) {
                                 var type = this.provider.name; if(this.type !== undefined) {
                                    type = this.type; }
                                 $(document.createElement("div")).addClass("provider").text(type).appendTo(result); }
                              if(this.icon !== undefined) {
                                 $(document.createElement("img")).attr("src", config.imagePath + this.icon).addClass("icon left").appendTo(result); }
                              if(args.showCheckBoxes == true) {
                                 $(document.createElement("input")).attr( {
                                    "type" : "checkbox", "title" : args.checkBoxTitle}
                                 ).addClass("check").appendTo(result).bind($.browser.msie ? "click" : "change", this, function(event) {
                                    event.data.checked = this.checked; if(args.checkHandler) {
                                       args.checkHandler.call(this, event); }
                                    }
                                 ); }
                              var link = utility.createLink().addClass("title").text(this.title !== undefined ? this.title : this.id).appendTo(result); if(args.resultClickHandler !== undefined) {
                                 link.bind("click", this, args.resultClickHandler); }
                              var description = undefined; if(this.description) {
                                 description = $(document.createElement("div")).addClass("description").text(this.description).appendTo(result); }
                              if(description !== undefined && this.author !== undefined) {
                                 $(document.createElement("div")).addClass("author").text(lang.AUTHOR + ": ").append($(document.createElement("span")).text(this.author).css("font-weight", "bold")).prependTo(description); }
                              result.append($(document.createElement("div")).addClass("clear")); result.appendTo(resultsPanel); }
                           );
                           }
                        else {
                           if(args.emptyHTML !== undefined) {
                              resultsPanel.append($(document.createElement("div")).addClass("result").html(args.emptyHTML));
                              }
                           }
                        if(args.updateHandler !== undefined) {
                           args.updateHandler.call(this);
                           }
                        }
                     var searchTimer;
                     function keypressHandler(event) {
                        window.clearTimeout(searchTimer);
                        if(this.value == "") {
                           return;
                           }
                        if(event.which == 13) {
                           search(this.value);
                           }
                        else {
                           searchTimer = window.setTimeout(function() {
                              search(searchField.val()); }
                           , 300);
                           }
                        }
                     this.search = function(term, providers) {
                        if(term && term.length > 0) {
                           searchField.removeClass("grey");
                           }
                        searchField.val(term);
                        if(term === undefined || term.length > 0) {
                           search(term, providers);
                           }
                        }
                     var requests;
                     var lastTerm;
                     var currentProviders;
                     function search(term, providers) {
                        if(term === undefined || term.length > 0) {
                           if(term === undefined) {
                              term = ""}
                           loader.addClass("show");
                           if(requests !== undefined) {
                              requests.each(function() {
                                 this.provider.abortSearch(this.request); }
                              );
                              requests = undefined;
                              }
                           requests = [];
                           items = [];
                           lastTerm = term;
                           if(providers !== undefined) {
                              currentProviders = providers;
                              }
                           else if(args.providers !== undefined) {
                              currentProviders = args.providers;
                              }
                           currentProviders.each(function() {
                              requests.push( {
                                 request : this.beginSearch(term, requestItemsHandler), provider : this}
                              ); }
                           );
                           }
                        }
                     this.getLastTerm = function() {
                        return lastTerm;
                        }
                     function requestItemsHandler(results) {
                        var request = this;
                        requests.each(function() {
                           if(this.request === request) {
                              requests.remove(this); return false; }
                           return true; }
                        );
                        items = items.concat(results);
                        updateUI();
                        if(requests.length == 0) {
                           if(args.searchFinishedHandler !== undefined) {
                              args.searchFinishedHandler(items);
                              }
                           loader.removeClass("show");
                           }
                        }
                     this.unload = function() {
                        items = undefined;
                        updateUI();
                        }
                     this.destroy = function() {
                        }
                     }
                  client.widgets.FilterSearch.prototype = new client.Widget;
                  client.widgets.FilterSearchInterface = function() {
                     }
                  client.widgets.FilterSearchInterface.prototype.beginSearch = function(searchTerm, callback) {
                     }
                  client.widgets.FilterSearchInterface.prototype.abortSearch = function(request) {
                     }
                  client.widgets.FilterSearchInterface.prototype.destroy = function() {
                     }
                  client.widgets.FilterSearchInterface.prototype.name = "Interface";
                  client.widgets.FilterSearchResult = function(title, description, tags, object) {
                     this.title = title;
                     this.description = description;
                     this.tags = tags;
                     this.object = object;
                     }
                  client.widgets.FilterSearchResult.prototype.title = undefined;
                  client.widgets.FilterSearchResult.prototype.description = undefined;
                  client.widgets.FilterSearchResult.prototype.tags = undefined;
                  client.widgets.FilterSearchResult.prototype.provider = undefined;
                  client.widgets.FilterSearchResult.prototype.object = undefined;
                  client.widgets.MediabirdSearchProviderType = {
                     group : 1, topic : 2, card : 4, marker : 8};
                  client.widgets.MediabirdSearchProvider = function(args) {
                     var me = this;
                     var server = args.server;
                     this.name = "Mediabird";
                     this.defaultType = args.defaultType !== undefined ? args.defaultType : client.widgets.MediabirdSearchProviderType.topic;
                     function convertResults(data) {
                        var resultItems = [];
                        if(data.success) {
                           var incoming = [];
                           var topics = server.getTopics();
                           var groups = server.getGroups();
                           var currentUser = server.getCurrentUser();
                           data.resultTopics.each(function() {
                              var result = this; var found = false; topics.each(function() {
                                 if(this.id == result.id) {
                                    found = true; incoming.push( {
                                       obj : this, highlight : this.access == client.data.TopicAccessConstants.owner, icon : "applications-office-small.png", author : this.author.name}
                                    ); return false; }
                                 }
                              ); if(found == false && result.group !== undefined && result.group >- 1) {
                                 groups.each(function() {
                                    if(this.id == result.group) {
                                       found = true; var isAccessible = this.access == client.data.GroupAccessConstants.allowJoin; if(isAccessible) {
                                          incoming.push( {
                                             obj : this, topicTitle : result.title, topicId : result.id, type : lang.GROUP, icon : "group-small.png"}
                                          ); }
                                       return false; }
                                    }
                                 ); }
                              }
                           );
                           data.resultCards.each(function() {
                              var result = this; var found = false; topics.each(function() {
                                 this.getAllCards().each(function() {
                                    if(this.id == result.id) {
                                       found = true; if(this.needsLoading || this.content != null) {
                                          incoming.push( {
                                             obj : this, highlight : this.step.topic.access == client.data.TopicAccessConstants.owner, icon : "map-view-small.png", author : this.step.topic.author.name}
                                          ); }
                                       return false; }
                                    }
                                 ); }
                              ); }
                           );
                           data.resultGroups.each(function() {
                              var result = this; var found = false; groups.each(function() {
                                 if(this.id == result.id) {
                                    found = true; incoming.push( {
                                       obj : this, highlight : server.getCurrentUser().getMembershipInGroup(this) != null, icon : "group-small.png"}
                                    ); return false; }
                                 }
                              ); }
                           );
                           incoming.each(function() {
                              var title; if(this.topicTitle !== undefined) {
                                 title = this.topicTitle; }
                              else if(this.obj.title !== undefined) {
                                 title = this.obj.title; }
                              else {
                                 title = this.obj.name; }
                              var item = new client.widgets.FilterSearchResult(title, this.obj.description !== undefined ? this.obj.description : this.obj.category, [], this.obj); if(this.topicId !== undefined) {
                                 item.topicId = this.topicId; }
                              item.icon = this.icon; item.highlight = this.highlight; item.provider = me; item.order = me.order; item.author = this.author; resultItems.push(item); }
                           );
                           }
                        return resultItems;
                        }
                     this.beginSearch = function(searchTerm, callback, type) {
                        var searchId;
                        if(type === undefined) {
                           type = this.defaultType;
                           }
                        var holder = new Object();
                        holder.request = server.searchDatabase(searchTerm, type, utility.createCallback(holder, function(data) {
                           var results = convertResults(data); callback.call(this.request, results); }
                        ));
                        return holder.request;
                        }
                     this.abortSearch = function(request) {
                        server.abortRequest(request);
                        }
                     this.destroy = function() {
                        me = undefined;
                        }
                     }
                  client.widgets.MediabirdSearchProvider.prototype = new client.widgets.FilterSearchInterface;
                  client.widgets.WikipediaSearchProvider = function() {
                     var me = this;
                     this.name = "Wikipedia";
                     this.beginSearch = function(searchTerm, callback) {
                        var wikipediaUrl = encodeURIComponent(lang.WIKI_AJAX_URL + encodeURIComponent(searchTerm) + "&namespace=0");
                        var holder = {
                           callback : callback};
                        holder.request = $.ajax( {
                           url : config.loadUrlPath + wikipediaUrl, method : "GET", dataType : "json", holder : holder, success : function(data) {
                              var items = []; for(var i = 0; i < data[1].length; i++) {
                                 var resultTitle = data[1][i]; var item = new client.widgets.FilterSearchResult(resultTitle, "", [], new client.data.WebResult(lang.WIKI_URL + encodeURI(resultTitle), resultTitle)); item.icon = "wikipedia-small.png"; item.provider = me; item.order = me.order; items.push(item); }
                              this.holder.callback.call(this.holder.request, items); }
                           , error : function(data) {
                              this.holder.callback.call(this.holder.request, []); }
                           }
                        );
                        return holder.request;
                        }
                     this.abortSearch = function(request) {
                        request.abort();
                        }
                     }
                  client.widgets.WikipediaSearchProvider.prototype = new client.widgets.FilterSearchInterface;
                  client.data.WebResult = function(url, title) {
                     this.url = url;
                     this.title = title;
                     }
                  client.data.WebResult.url = undefined;
                  client.data.WebResult.title = undefined;
                  client.PagePlugin = function() {
                     }
                  client.PagePlugin.prototype.page = null;
                  client.PagePlugin.prototype.load = function() {
                     };
                  client.PagePlugin.prototype.unload = function() {
                     };
                  client.PagePlugin.prototype.getPreview = function() {
                     return $("<div/>");
                     };
                  client.PagePlugin.prototype.handleButtonClick = function() {
                     }
                  client.PagePlugin.prototype.onPluginLoad = function(plugin) {
                     };
                  client.PagePlugin.prototype.onPluginRemove = function(plugin) {
                     };
                  client.pageplugins.LogonForm = function() {
                     client.PagePlugin.call(this);
                     var me;
                     this.load = function() {
                        me = this;
                        utility.setupAlerts();
                        client.PagePlugin.prototype.load.call(this);
                        };
                     this.loadLogon = function() {
                        loadUI();
                        }
                     var mainPlugin;
                     this.gotoMainView = function() {
                        mainPlugin = new client.pageplugins.MainView( {
                           sessionEndCallback : utility.createCallback(this, sessionEndHandler)}
                        );
                        unloadUI();
                        this.page.loadPagePlugin(mainPlugin);
                        }
                     function sessionEndHandler() {
                        loadUI();
                        this.page.unloadPagePlugin(mainPlugin);
                        mainPlugin = null;
                        }
                     this.unload = function() {
                        unloadUI();
                        mainPlugin = null;
                        me = null;
                        utility.destroyAlerts();
                        client.PagePlugin.prototype.unload.call(this);
                        }
                     function loadUI() {
                        $(document).bind("click", hidePanels);
                        maincont = $(document.createElement("div")).addClass("mb-cont");
                        maindesk = $(document.createElement("div")).addClass("mb-logon");
                        $(document.createElement("h1")).text(lang.MEDIABIRD_TITLE).appendTo(maindesk);
                        loginForm = createTable("login");
                        $(document.createElement("tr")).append($(document.createElement("td"))).append($(document.createElement("td")).append(utility.createToolbar().append(utility.createMenuItem("dialog-apply.png", null, lang.LOGON).bind("click", loginButtonHandler).attr("accesskey", "L")))).appendTo(loginForm.content);
                        $(document.createElement("tr")).append($(document.createElement("td"))).append($(document.createElement("td")).append(utility.createToolbar().append(utility.createMenuItem("go-up.png", null, lang.SIGN_UP).bind("click", signupButtonHandler).attr("accesskey", "R")))).appendTo(loginForm.content);
                        $(document.createElement("tr")).append($(document.createElement("td"))).append($(document.createElement("td")).append(utility.createToolbar().append(utility.createMenuItem("dialog-information.png", null, lang.GET_PASSWORD).bind("click", lostPasswordButtonHandler).attr("accesskey", "G")))).appendTo(loginForm.content);
                        $(document.createElement("form")).attr( {
                           action : "server/logon.php", method : "POST", target : "_blank"}
                        ).bind("submit", loginButtonHandler).append(loginForm.content.css( {
                           "float" : "right", "margin-left" : 30, "padding-left" : 10, "border-left" : "1px solid #999"}
                        )).append($(document.createElement("input")).css("display", "none").attr( {
                           type : "submit"}
                        )).appendTo(maindesk);
                        $(lang.MEDIABIRD_DESCRIPTION).appendTo(maindesk);
                        if(($.browser.msie && $.browser.version < "7.0") || ($.browser.safari && $.browser.version < "400") || (!$.browser.msie &&!$.browser.mozilla &&!$.browser.safari)) {
                           $(lang.BROWSER_BAD_TEXT).appendTo(maindesk);
                           }
                        maindesk.prependTo(maincont);
                        maincont.prependTo(me.page.container);
                        resetForm();
                        }
                     function retrieve(email, captcha) {
                        me.page.server.retrievePassword(email, captcha, utility.createCallback(me, retrieveCallback));
                        }
                     function retrieveCallback(data) {
                        if(data.success) {
                           alert(lang.PASSWORD_SENT);
                           resetForm();
                           hidePanels();
                           }
                        else {
                           if(lostPassForm) {
                              loadCaptcha(lostPassForm.captchaImage);
                              }
                           if(data.error == "wrongcaptcha") {
                              alert(lang.WRONG_SECURITY);
                              }
                           else if(data.error == "wrongemail") {
                              alert(lang.INVALID_EMAIL);
                              }
                           else if(data.error == "nosuchuser") {
                              alert(lang.PASSWORD_NO_USER);
                              }
                           else if(data.error == "errorsending") {
                              alert(lang.PASSWORD_INVALID);
                              }
                           else if(data.error == "disabled") {
                              alert(lang.ERROR_FUNCTION_DISABLED);
                              }
                           else {
                              alert(lang.PASSWORD_ERROR);
                              }
                           }
                        }
                     function signup(name, password, email, captcha) {
                        me.page.server.signUp(name, password, email, captcha, utility.createCallback(me, signupCallback));
                        }
                     function signupCallback(data) {
                        if(data.success) {
                           if(data.emailsent) {
                              alert(lang.CONFIRM_REQUEST_SENT);
                              }
                           else {
                              alert(lang.ACCOUNT_CREATED);
                              }
                           resetForm();
                           }
                        else {
                           if(signupForm) {
                              loadCaptcha(signupForm.captchaImage);
                              }
                           if(data.error == "wrongcaptcha") {
                              alert(lang.WRONG_SECURITY);
                              }
                           else if(data.error == "wrongemail") {
                              alert(lang.INVALID_EMAIL);
                              }
                           else if(data.error == "emailnotunique") {
                              alert(lang.USER_EMAIL_TAKEN);
                              }
                           else if(data.error == "namenotunique") {
                              alert(lang.USER_NAME_TAKEN);
                              }
                           else if(data.error == "errorsending") {
                              alert(lang.ERROR_SENDING_CONFIRM);
                              }
                           else if(data.error == "database") {
                              alert(lang.ERROR_CREATING_USER);
                              }
                           else if(data.error == "disabled") {
                              alert(lang.ERROR_FUNCTION_DISABLED);
                              }
                           }
                        }
                     function login(name, password) {
                        me.page.server.openSession(name, password, utility.createCallback(me, loginCallback));
                        }
                     function loginCallback(data) {
                        if(data.success) {
                           me.gotoMainView();
                           }
                        else {
                           if(data.error == "passwrong") {
                              alert(lang.WRONG_PASSWORD);
                              }
                           else if(data.error == "sessionerror") {
                              alert(lang.SESSION_CREATION_ERROR);
                              }
                           else if(data.error == "disabled") {
                              alert(lang.NOT_CONFIRMED);
                              }
                           else {
                              alert(lang.SESSION_CREATION_ERROR);
                              }
                           }
                        }
                     function loginButtonHandler(event) {
                        event.preventDefault();
                        event.stopPropagation();
                        hidePanels();
                        var nameOkay = (loginForm.nameField.val() != "");
                        var passOkay = (loginForm.passField.val() != "");
                        loginForm.nameError.css("display", !nameOkay ? "block" : "none");
                        loginForm.passError.css("display", !passOkay ? "block" : "none");
                        if(nameOkay && passOkay) {
                           login(loginForm.nameField.val(), loginForm.passField.val());
                           loginForm.passField.val("");
                           }
                        }
                     var lostPassForm;
                     function lostPasswordButtonHandler(event) {
                        event.stopPropagation();
                        if(!lostPassForm) {
                           resetForm();
                           var pos = {
                              };
                           pos.top = this.offsetHeight + 5;
                           pos.left = this.offsetWidth - 375;
                           lostPassForm = createTable("lost");
                           lostPassPanel = utility.createPanelDialog(lostPassForm.content, pos, lostDialogHandler).css("width", "370px").appendTo(this.parentNode);
                           loadCaptcha(lostPassForm.captchaImage);
                           }
                        }
                     function lostDialogHandler(event) {
                        event.stopPropagation();
                        var button = event.data;
                        switch(button.action) {
                           case"ok" : var emailOkay = (lostPassForm.emailField.val() != "");
                           var captchaOkay = (lostPassForm.captchaField.val().length == 6);
                           lostPassForm.emailError.css("display", !emailOkay ? "block" : "none");
                           lostPassForm.captchaError.css("display", !captchaOkay ? "block" : "none");
                           if(emailOkay && captchaOkay) {
                              retrieve(lostPassForm.emailField.val(), lostPassForm.captchaField.val());
                              lostPassForm.emailField.val("");
                              }
                           else {
                              return;
                              }
                           break;
                           case"cancel" : resetForm();
                           break;
                           }
                        }
                     var signupForm;
                     function signupButtonHandler(event) {
                        event.stopPropagation();
                        if(!signupForm) {
                           resetForm();
                           var pos = {
                              };
                           pos.top = this.offsetHeight + 5;
                           pos.left = this.offsetWidth - 375;
                           signupForm = createTable("signup");
                           signupPanel = utility.createPanelDialog(signupForm.content, pos, signupDialogHandler).css("width", 380).appendTo(this.parentNode);
                           loadCaptcha(signupForm.captchaImage);
                           }
                        }
                     function signupDialogHandler(event) {
                        var button = event.data;
                        switch(button.action) {
                           case"ok" : var nameOkay = signupForm.nameField.val() != "";
                           var passOkay = signupForm.passField.val() != "";
                           var pass2Okay = signupForm.passField2.val() != "" && signupForm.passField2.val() == signupForm.passField.val();
                           var emailOkay = signupForm.emailField.val() != "";
                           var captchaOkay = signupForm.captchaField.val().length == 6;
                           var termsOkay = signupForm.termsCheckbox.attr("checked") == true;
                           signupForm.nameError.css("display", !nameOkay ? "block" : "none");
                           signupForm.passError.css("display", !passOkay ? "block" : "none");
                           signupForm.passError2.css("display", !pass2Okay ? "block" : "none");
                           signupForm.emailError.css("display", !emailOkay ? "block" : "none");
                           signupForm.captchaError.css("display", !captchaOkay ? "block" : "none");
                           signupForm.termsError.css("display", !termsOkay ? "block" : "none");
                           if(nameOkay && passOkay && emailOkay && captchaOkay && termsOkay) {
                              signup(signupForm.nameField.val(), signupForm.passField.val(), signupForm.emailField.val(), signupForm.captchaField.val());
                              signupForm.passField.val("");
                              signupForm.passField2.val("");
                              }
                           else {
                              return;
                              }
                           break;
                           case"cancel" : resetForm();
                           break;
                           }
                        }
                     var signupPanel, lostPassPanel;
                     function createTable(type) {
                        var content = $(document.createElement("table")).addClass("logonform");
                        var ret = {
                           content : content};
                        if(type != "lost") {
                           $(document.createElement("tr")).append($(document.createElement("td")).css("text-align", "right").append($(document.createElement("label")).text(lang.NAME).attr( {
                              "for" : "nameField" + type, "accesskey" : 'n'}
                           ))).append($(document.createElement("td")).append(ret.nameField = $(document.createElement("input")).attr("type", "text").attr( {
                              id : "nameField" + type, size : "15"}
                           )).append(ret.nameError = $(document.createElement("span")).addClass("error").text(lang.NAME_MISSING))).appendTo(content);
                           $(document.createElement("tr")).append($(document.createElement("td")).css("text-align", "right").append($(document.createElement("label")).text(lang.PASSWORD).attr( {
                              "for" : "passField" + type, "accesskey" : lang.PASSWORD_ACCESS_KEY}
                           ))).append($(document.createElement("td")).append(ret.passField = $(document.createElement("input")).attr("type", "text").attr( {
                              type : "password", id : "passField" + type, size : "15"}
                           )).append(ret.passError = $(document.createElement("span")).addClass("error").text(lang.PASS_MISSING))).appendTo(content);
                           }
                        if(type == "signup") {
                           $(document.createElement("tr")).append($(document.createElement("td")).css("text-align", "right").append($(document.createElement("label")).text(lang.PASSWORD_AGAIN).attr( {
                              "for" : "passField2" + type}
                           ))).append($(document.createElement("td")).append(ret.passField2 = $(document.createElement("input")).attr( {
                              type : "password", id : "passField2" + type, size : "15"}
                           )).append(ret.passError2 = $(document.createElement("span")).addClass("error").text(lang.PASS_NOT_MATCHING))).appendTo(content);
                           $(document.createElement("tr")).append($(document.createElement("td")).css("text-align", "right").append($(document.createElement("span")).text(lang.TERMS))).append($(document.createElement("td")).append(ret.termsCheckbox = $(document.createElement("input")).attr( {
                              type : "checkbox", size : "15"}
                           )).append($(document.createElement("span")).html(lang.TERMS_ACCEPT)).append(ret.termsError = $(document.createElement("span")).addClass("error").text(lang.TERMS_MISSING))).appendTo(content);
                           }
                        if(type != "login") {
                           $(document.createElement("tr")).append($(document.createElement("td")).css("text-align", "right").append($(document.createElement("label")).text(lang.EMAIL).attr( {
                              "for" : "emailField" + type}
                           ))).append($(document.createElement("td")).append(ret.emailField = $(document.createElement("input")).attr( {
                              type : "text", id : "emailField" + type, size : "15"}
                           )).append(ret.emailError = $(document.createElement("span")).addClass("error").text(lang.EMAIL_MISSING))).appendTo(content);
                           $(document.createElement("tr")).append($(document.createElement("td")).css( {
                              "vertical-align" : "top", "padding-top" : "5px"}
                           ).css("text-align", "right").append($(document.createElement("label")).text(lang.CAPTCHA).attr( {
                              "for" : "captchaField" + type}
                           ))).append($(document.createElement("td")).append(utility.createToolbar().append(ret.captchaField = $(document.createElement("input")).attr( {
                              type : "text", id : "captchaField" + type, size : "6", maxlength : "6"}
                           )).append($(document.createElement("img")).attr( {
                              src : config.imagePath + "go-previous.png"}
                           ).css( {
                              "float" : "left"}
                           )).append(ret.captchaImage = $(document.createElement("img")).css( {
                              "float" : "left"}
                           )).append(utility.createButtonSeparator()).append(utility.createButton("view-refresh.png", null, lang.GET_NEW_CAPTCHA).bind("click", ret.captchaImage, loadCaptcha))).append(ret.captchaError = $(document.createElement("span")).addClass("error").text(lang.CAPTCHA_MISSING))).appendTo(content);
                           }
                        return ret;
                        }
                     function hidePanels(event) {
                        if(signupForm) {
                           signupPanel.remove();
                           signupForm = null;
                           }
                        if(lostPassForm) {
                           lostPassPanel.remove();
                           lostPassForm = null;
                           }
                        }
                     function resetForm() {
                        hidePanels();
                        loginForm.nameField.val("");
                        loginForm.passField.val("");
                        loginForm.nameError.css("display", "none");
                        loginForm.passError.css("display", "none");
                        }
                     var maindesk, maincont, mainform;
                     var loginForm;
                     this.justEnabled = function() {
                        if(maindesk) {
                           maindesk.children(":first").after($(document.createElement("h3")).text(lang.JUST_ENABLED));
                           }
                        }
                     function loadCaptcha(event) {
                        var image = event instanceof jQuery ? event : event.data;
                        image.attr( {
                           src : "server/captcha.php?q=" + (new Date()).getTime()}
                        );
                        }
                     function unloadUI() {
                        $(document).unbind("click", hidePanels);
                        if(maindesk) {
                           maindesk.remove();
                           maincont.remove();
                           }
                        }
                     }
                  client.pageplugins.LogonForm.prototype = new client.PagePlugin;
                  client.pageplugins.MainViewToolConstants = {
                     none : 0, trainer : 1, mapView : 2, profileEditor : 3, newsView : 4, hallOfFame : 5, goalManager : 6, notesTool : 7, taskManager : 8, sharingManager : 9, sessionView : 10, studentFinder : 11, searchView : 12};
                  client.pageplugins.MainViewButtonConstants = {
                     home : 0, organization : 1, community : 2, search : 3};
                  client.pageplugins.MainView = function(args) {
                     var CHECK_OUT_INTERVAL = 45;
                     var BUTTON_HOVER_DELAY = 1000;
                     var REVISION_CHECK_PAUSE = 10000;
                     var INITIAL_CARD_COUNT = 4;
                     var MIN_STEPS_FOR_LEVEL_HANDLERS = 4;
                     var WAIT_BEFORE_AUTO_FADE = 2000;
                     var MAX_HISTORY_DISPLAY_COUNT = 10;
                     client.PagePlugin.call(this);
                     var currentCard = null;
                     var display = null;
                     var trainer = null;
                     var currentTool = client.pageplugins.MainViewToolConstants.none;
                     var me;
                     var settings, currentBalloon, balloonConfig;
                     this.load = function() {
                        me = this;
                        setupUI();
                        utility.destroyAlerts();
                        utility.alertSave = window.alert;
                        window.alert = customAlert;
                        utility.globalSave = function() {
                           me.saveCard();
                           }
                        var args = {
                           container : desktop.fullPreview};
                        views = {
                           home : new client.pageplugins.Home(args), organization : new client.pageplugins.Organization(args), search : new client.pageplugins.Search(args), community : new client.pageplugins.Community(args)};
                        for(var i in views) {
                           me.page.loadPagePlugin(views[i]);
                           }
                        views[client.pageplugins.MainViewButtonConstants.home] = views.home;
                        views[client.pageplugins.MainViewButtonConstants.community] = views.community;
                        views[client.pageplugins.MainViewButtonConstants.search] = views.search;
                        views[client.pageplugins.MainViewButtonConstants.organization] = views.organization;
                        var currentUser = this.page.server.getCurrentUser();
                        settings = currentUser.settings;
                        if(settings.desktopBalloons === undefined) {
                           settings.desktopBalloons = {
                              };
                           }
                        balloonConfig = settings.desktopBalloons;
                        this.page.server.loadGroupList(utility.createCallback(this, listGroupsHandler), true);
                        this.page.server.loadTopicList(utility.createCallback(this, listTopicsHandler));
                        client.PagePlugin.prototype.load.call(this);
                        };
                     function customAlert(str) {
                        if(str !== undefined) {
                           str = str.toString();
                           desktop.alertContainer.fadeIn("normal", function() {
                              desktop.alertContainer.focusFirstEditable(); }
                           );
                           remove = utility.createLink(lang.REMOVE_ALERT).click(function(event) {
                              event.stopPropagation(); var me = $(this); var container = me.parent().parent(); me.parent().remove(); if(container.children().length == 1) {
                                 container.fadeOut(); }
                              else {
                                 desktop.alertContainer.focusFirstEditable(); }
                              }
                           )var item = $(document.createElement("p")).text(str).addClass("alert-item").append(remove).appendTo(desktop.alertContainer);
                           }
                        }
                     function listTopicsHandler(data) {
                        if(!data.success) {
                           alert(lang.COULD_NOT_LOAD_TOPICS);
                           }
                        else {
                           this.page.server.registerSyncHandler("topics", topicsChangedHandler);
                           this.page.server.registerSyncHandler("topic", topicsChangedHandler);
                           setupHover();
                           var cardId;
                           if(config.customLoadCard !== undefined) {
                              cardId = config.customLoadCard;
                              delete config.customLoadCard;
                              }
                           else if(settings.lastCardId !== undefined) {
                              cardId = settings.lastCardId;
                              }
                           openTopicById(cardId);
                           if(utility.setupIntro !== undefined) {
                              showIntroOverlay(utility.setupIntro);
                              }
                           }
                        }
                     function openTopicById(cardId) {
                        var topics = me.page.server.getTopics();
                        var found = false;
                        var candidate;
                        topics.each(function() {
                           if(this.getStepCount() > 0 && candidate === undefined) {
                              candidate = this; }
                           if(cardId !== undefined) {
                              this.each(function() {
                                 this.each(undefined, function() {
                                    if(this.id == cardId) {
                                       me.openCard(this, this.step.topic.isEditable()); found = true; return false; }
                                    }
                                 ); if(found) {
                                    return false; }
                                 }
                              ); if(found) {
                                 return false; }
                              }
                           }
                        );
                        if(found == false) {
                           if(candidate !== undefined) {
                              me.openTopic(candidate);
                              }
                           else {
                              createTopic();
                              }
                           }
                        }
                     function listGroupsHandler(data) {
                        if(!data.success) {
                           alert(lang.COULD_NOT_LOAD_GROUPS);
                           }
                        }
                     function topicsChangedHandler() {
                        if(currentCard != null && display.isEditor()) {
                           display.updateTitle();
                           }
                        var topics = me.page.server.getTopics();
                        lastActions.clone().each(function() {
                           if(((this.relation instanceof client.data.Topic) && topics.indexOf(this.relation) ==- 1) || ((this.relation instanceof client.data.Card) && (this.relation.step == null || topics.indexOf(this.relation.step.topic) ==- 1))) {
                              lastActions.remove(this); }
                           }
                        );
                        var foundTopic = false, foundCard = false;
                        topics.each(function() {
                           if(this === currentTopic) {
                              foundTopic = true; var cards = this.getAllCards(); cards.each(function() {
                                 if(this === currentCard) {
                                    foundCard = true; return false; }
                                 }
                              ); return false; }
                           }
                        );
                        if(!foundCard) {
                           display.closeCard();
                           currentCard = null;
                           if(foundTopic == true) {
                              me.openCard(currentTopic.getStep(0).getMainCard(), currentTopic.isEditable());
                              }
                           else {
                              openTopicById();
                              }
                           }
                        else {
                           if(currentTopic.isShared() && currentTopic.access == client.data.TopicAccessConstants.owner &&!currentCard.checkedOut) {
                              triggerCheckOut();
                              }
                           }
                        updateProgress(true);
                        }
                     function preloadTopics(topics) {
                        var cardsToBeLoaded = [];
                        for(var i = 0; i < topics.length; i++) {
                           var topic = topics[i];
                           var cards = topic.getAllCards();
                           for(var j = 0; j < cards.length; j++) {
                              var card = cards[j];
                              if(card.needsLoading) {
                                 cardsToBeLoaded.push(card);
                                 }
                              }
                           }
                        if(cardsToBeLoaded.length > 0) {
                           me.page.server.loadCardsContents(cardsToBeLoaded, utility.createCallback());
                           }
                        }
                     var desktop;
                     var container;
                     var views;
                     function setupUI() {
                        setupLogoutLink();
                        container = $(document.createElement("div")).addClass("mb-cont");
                        desktop = $(document.createElement("div")).appendTo(container);
                        me.page.container.append(container);
                        views = {
                           };
                        setupDesktop(desktop);
                        display = new client.pageplugins.NoteDisplay( {
                           container : desktop.noteContainer, header : desktop.headerContainer, pen : desktop.highlightingPen, clipboardBox : desktop.clipboardBox, flashCardBox : desktop.flashCardBox, mainClickHandler : mainClickHandler}
                        );
                        me.page.loadPagePlugin(display);
                        }
                     var logoutNode;
                     function setupLogoutLink() {
                        var label;
                        if(me.page.header !== undefined) {
                           var user = me.page.server.getCurrentUser();
                           var username = user.name.substr(0, 1).toUpperCase() + user.name.substr(1);
                           label = $(document.createElement("span")).addClass("user-info").text(username + " – ").prependTo(me.page.header);
                           logoutNode = $(utility.createLink(lang.LOGOUT)).addClass("logout-link").prependTo(me.page.header);
                           }
                        else {
                           var logoutLink = $("a#mediabirdLogout");
                           if(logoutLink.length == 1) {
                              logoutNode = logoutLink;
                              }
                           }
                        if(logoutNode !== undefined) {
                           logoutNode.bind("click", logoutHandler);
                           if(label !== undefined) {
                              logoutNode = logoutNode.add(label);
                              }
                           }
                        }
                     function logoutHandler(event) {
                        if(event !== undefined) {
                           event.stopPropagation();
                           }
                        me.hideView();
                        var state = saveCard();
                        if(state == false) {
                           closeAfterCallback = true;
                           }
                        else {
                           closeAfterCallback = false;
                           closeSession();
                           }
                        }
                     function mainClickHandler(event) {
                        desktop.overlays.fadeOut();
                        }
                     function setupDesktop(desktop) {
                        desktop.addClass('mb-desk');
                        desktop.buttonLeft = utility.createLink().appendTo(desktop).addClass('button panel-hover button-left full-border').attr("accesskey", "a").bind("click", false, naviHandler).addClass("ignore").hide();
                        desktop.buttonLeft.attr("title", lang.NAVI_LEFT_EXPLANATION);
                        desktop.buttonMapView = utility.createLink().appendTo(desktop).addClass('button panel-hover button-mapview').attr("accesskey", "m");
                        desktop.buttonMapView.attr("title", lang.MAP_VIEW_EXPLANATION);
                        desktop.buttonRight = utility.createLink().appendTo(desktop).addClass('button panel-hover button-right full-border').attr("accesskey", "d").bind("click", true, naviHandler);
                        desktop.buttonRight.attr("title", lang.NAVI_RIGHT_EXPLANATION);
                        desktop.flashCardBox = utility.createLink().appendTo(desktop).addClass('button flash-card-box');
                        desktop.flashCardBox.attr("title", lang.MEMORIZE_EXPLANATION);
                        if(config.reduceFeatureSet) {
                           desktop.flashCardBox.addClass("reduced");
                           }
                        desktop.clipboardBox = utility.createLink().appendTo(desktop).addClass('button clipboard-box');
                        desktop.clipboardBox.attr("title", lang.CLIPBOARD_EXPLANATION);
                        if(config.reduceFeatureSet) {
                           desktop.clipboardBox.addClass("ignore reduced");
                           }
                        desktop.highlightingPen = utility.createLink().appendTo(desktop).addClass('button highlighting-pen');
                        desktop.tabLevelMain = utility.createLink().appendTo(desktop).addClass('button tab-level-main ' + lang.code).bind("click", client.data.LevelConstants.main, levelHandler);
                        desktop.tabLevelIllustrative = utility.createLink().appendTo(desktop).addClass('button tab-level-illustrative ' + lang.code).bind("click", client.data.LevelConstants.illustrative, levelHandler).attr("accesskey", "s").hide();
                        desktop.tabLevelAdvanced = utility.createLink().appendTo(desktop).addClass('button tab-level-advanced ' + lang.code).bind("click", client.data.LevelConstants.advanced, levelHandler).attr("accesskey", "w").hide();
                        desktop.cardTools = desktop.children().disableContextMenu();
                        desktop.alertInfo = utility.createLink().appendTo(desktop).hide().addClass('button alert-info').attr("accesskey", "?").disableContextMenu();
                        desktop.alertContainer = $(document.createElement("div")).appendTo(desktop).addClass('alert-container').hide();
                        desktop.alertContainer.append($(document.createElement("h2")).text(lang.ALERT_HEADER));
                        desktop.shoutboxLink = utility.createLink().appendTo(desktop).addClass('button shoutbox-link').attr( {
                           "accesskey" : "!", "title" : lang.GIVE_US_FEEDBACK}
                        ).disableContextMenu();
                        desktop.noteContainer = $(document.createElement("div")).appendTo(desktop).addClass('note-container');
                        desktop.headerContainer = $(document.createElement("div")).appendTo(desktop).addClass('header-container');
                        desktop.fullContainer = $(document.createElement("div")).appendTo(desktop).addClass('full-container').hide();
                        desktop.previewContainer = $(document.createElement("div")).appendTo(desktop).addClass('container-hover preview-container').hide();
                        desktop.previewContainer.add(desktop.fullContainer);
                        desktop.progressBox = $(document.createElement("div")).addClass("progress-box").appendTo(desktop).hide();
                        desktop.overlays = desktop.previewContainer.add(desktop.progressBox);
                        desktop.buttonHome = utility.createLink();
                        desktop.buttonOrganization = utility.createLink();
                        /*desktop.buttonCommunity = utility.createLink().appendTo(desktop).addClass('button panel-hover button-community');
                        desktop.buttonCommunity.attr("title", lang.COMMUNITY_EXPLANATION);*/
                        desktop.buttonSearch = utility.createLink().appendTo(desktop).addClass('button panel-hover button-search');
                        desktop.buttonSearch.attr("title", lang.SEARCH_EXPLANATION);
                        if(config.reduceFeatureSet) {
                           /*desktop.buttonCommunity.hide();*/
                           desktop.buttonSearch.hide();
                           if(config.fullLocationFromOverlay !== undefined) {
                              desktop.flashCardBox.attr("href", config.fullLocationFromOverlay);
                              if(config.reference !== undefined && config.reference.target !== undefined) {
                                 desktop.flashCardBox.attr("target", config.reference.target);
                                 }
                              else {
                                 desktop.flashCardBox.attr("target", "_parent");
                                 }
                              desktop.flashCardBox.attr("title", lang.SWITCH_TO_FULL);
                              desktop.flashCardBox.bind("click", function(event) {
                                 event.stopImmediatePropagation(); if(saveCard() == false) {
                                    event.preventDefault(); switchFullAfterSave = true; }
                                 }
                              );
                              }
                           }
                        else {
                           desktop.cardTools = desktop.cardTools./*add(desktop.buttonCommunity).*/add(desktop.buttonSearch);
                           }
                        desktop.flashCardBox.bind("click", flashCardBoxHandler);
                        desktop.previewContainer.bind("click", function(event) {
                           event.stopPropagation(); }
                        );
                        if(config.feedbackPath === undefined || config.feedbackPath == "internal") {
                           desktop.shoutboxLink.bind("click", shoutboxLinkHandler);
                           }
                        else {
                           desktop.shoutboxLink.attr( {
                              target : "_blank", href : config.feedbackPath}
                           );
                           }
                        }
                     function setupHover() {
                        utility.setupButtonHover(desktop.buttonMapView, desktop.progressBox, function() {
                           if(currentTopic != null && (currentTool == client.pageplugins.MainViewToolConstants.none || researchMode)) {
                              checkTopicRevision(); return true; }
                           }
                        , WAIT_BEFORE_AUTO_FADE, - 1);
                        utility.setupButtonHover(/*desktop.buttonCommunity.*/add(desktop.buttonSearch), desktop.previewContainer, function() {
                           if(desktop.buttonSearch.index(this) !=- 1) {
                              showPreview(client.pageplugins.MainViewButtonConstants.search); return true; }
                           /*if(desktop.buttonCommunity.index(this) !=- 1) {
                              showPreview(client.pageplugins.MainViewButtonConstants.community); return true; }*/
                           if(desktop.buttonHome.index(this) !=- 1) {
                              showPreview(client.pageplugins.MainViewButtonConstants.home); return true; }
                           if(desktop.buttonOrganization.index(this) !=- 1) {
                              showPreview(client.pageplugins.MainViewButtonConstants.organization); return true; }
                           }
                        , WAIT_BEFORE_AUTO_FADE, - 1, function() {
                           currentPreviewTool = client.pageplugins.MainViewButtonConstants.none; }
                        );
                        }
                     function topicRevisionCallback(data) {
                        if(data.success) {
                           if(data.topic !== undefined) {
                              if(data.topic === currentTopic) {
                                 topicsChangedHandler();
                                 }
                              if((currentMap !== undefined) && (currentMap.topic == data.topic)) {
                                 currentMap.map.unload();
                                 var args = {
                                    topic : currentMap.topic, isEditor : currentMap.isEditor}
                                 currentMap.changes = {
                                    };
                                 $(".field-title", currentMap.toolbar).text(data.topic.title);
                                 $(".field-category", currentMap.toolbar).text(data.topic.category);
                                 currentMap.map.load(args);
                                 }
                              updateProgress();
                              }
                           else {
                              }
                           }
                        }
                     this.requestContainer = function(tool, callback, noSave) {
                        hideView(true);
                        if(noSave === undefined ||!noSave) {
                           me.saveCard();
                           }
                        showTools(false);
                        prepareFullContainer();
                        currentToolCallback = callback;
                        currentTool = tool;
                        return desktop.fullContainer;
                        }
                     var floatObj;
                     var researchMode;
                     this.requestFloat = function(closeHandler, createCloser, persistent, hideFull, extraWide) {
                        hideFloat(true);
                        var content = $(document.createElement("div")).addClass("full-overlay");
                        var contNode = desktop.fullContainer.get(0);
                        var pos = {
                           left : contNode.offsetLeft + contNode.parentNode.offsetLeft, top : contNode.offsetTop + contNode.parentNode.offsetTop};
                        pos.width = config.RESEARCH_PANEL_WIDTH;
                        content.css(pos).appendTo(desktop.fullContainer.parent().parent());
                        var shiftObj = $.setupPanelShifting(desktop.fullContainer.parent(), content, undefined, config.SHIFT_RESEARCH_LEFT, (extraWide && config.SHIFT_RESEARCH_RIGHT_WIDE) ? config.SHIFT_RESEARCH_RIGHT_WIDE : config.SHIFT_RESEARCH_RIGHT);
                        shiftObj.closeHandler = closeHandler;
                        if(hideFull) {
                           showTools(true);
                           researchMode = true;
                           desktop.fullContainer.fadeOut();
                           shiftObj.closeHandler2 = function() {
                              desktop.fullContainer.fadeIn();
                              showTools(false);
                              researchMode = false;
                              };
                           }
                        shiftObj.callback = function() {
                           if(this.closeHandler !== undefined) {
                              this.closeHandler.call(this);
                              }
                           if(this.closeHandler2 !== undefined) {
                              this.closeHandler2.call(this);
                              }
                           };
                        if(createCloser === undefined || createCloser) {
                           var closer = utility.createCloser().appendTo(content);
                           closer.one("click", shiftObj, function(event) {
                              event.data.restore(); }
                           );
                           var minimizer = utility.createCloser().addClass("minimize").attr("title", lang.MINIMIZE).appendTo(content);
                           minimizer.bind("click", shiftObj, function(event) {
                              var me = $(this); if(!me.hasClass("maximize")) {
                                 event.data.content.animate( {
                                    width : 180, height : 10, top : 337}
                                 ); me.attr("title", lang.MAXIMIZE); }
                              else {
                                 event.data.content.animate( {
                                    width : config.RESEARCH_PANEL_WIDTH, height : 367, top : 12}
                                 ); me.attr("title", lang.MINIMIZE); }
                              $(this).toggleClass("maximize"); }
                           );
                           }
                        floatObj = shiftObj;
                        return floatObj;
                        }
                     function hideFloat(immediate) {
                        hideBalloon();
                        if(floatObj !== undefined) {
                           floatObj.restore(undefined, immediate);
                           floatObj = undefined;
                           }
                        }
                     var currentPreviewTool = client.pageplugins.MainViewButtonConstants.none;
                     function showPreview(tool) {
                        if(currentPreviewTool == tool) {
                           return;
                           }
                        resetPreviewContainer();
                        currentPreviewTool = tool;
                        desktop.previewContainer.append(views[tool].getPreview( {
                           topic : currentTopic}
                        ));
                        }
                     function resetPreviewContainer() {
                        desktop.previewContainer.empty();
                        currentPreviewTool = client.pageplugins.MainViewButtonConstants.none;
                        }
                     function levelHandler(event) {
                        if(currentCard == null) {
                           return;
                           }
                        var level = event.data;
                        var newCards = currentCard.step.getCards(level);
                        if(newCards.length > 0) {
                           var card;
                           if(level == client.data.LevelConstants.illustrative) {
                              card = newCards[0];
                              }
                           else {
                              card = newCards[newCards.length - 1];
                              }
                           if(card !== currentCard) {
                              me.openCard(card, card.step.topic.isEditable());
                              }
                           desktop.progressBox.fadeIn();
                           }
                        else {
                           if(currentCard.step.topic.isEditable()) {
                              insertCard(currentTopic, new client.data.Card(), currentTopic.getIndexOfStep(currentCard.step), level);
                              desktop.progressBox.fadeIn();
                              }
                           }
                        }
                     var lastTopicRefresh;
                     function checkTopicRevision(force) {
                        if(!currentTopic.isShared()) {
                           return;
                           }
                        var end = new Date();
                        if(force || lastTopicRefresh === undefined || (end.getTime() - lastTopicRefresh.getTime() > REVISION_CHECK_PAUSE)) {
                           me.page.server.checkTopicRevision(currentTopic, utility.createCallback(me, topicRevisionCallback));
                           lastTopicRefresh = end;
                           }
                        return true;
                        }
                     this.showMap = function() {
                        mapViewHandler( {
                           }
                        );
                        }
                     var currentMap;
                     function mapViewHandler(event) {
                        if(currentCard == null) {
                           return;
                           }
                        checkTopicRevision(true);
                        hideView(true);
                        var container = me.requestContainer(client.pageplugins.MainViewToolConstants.mapView, updateTopicChanges);
                        $("a.close-button", container).attr("title", lang.SAVE_CLOSE);
                        var heading;
                        if(event.data === undefined || event.data.heading === undefined) {
                           if(currentCard.step.topic.isStructurable()) {
                              heading = lang.EDIT_MAP;
                              }
                           else if(currentCard.step.topic.isEditable()) {
                              heading = lang.EXTEND_MAP;
                              }
                           else {
                              heading = lang.SHOW_MAP;
                              }
                           }
                        else {
                           heading = event.data.heading;
                           }
                        $(heading).appendTo(desktop.fullContainer);
                        currentMap = client.widgets.MapView.createMapView(currentTopic, currentCard.step.topic.isEditable(), container, null, me.page.server.getTopics(), {
                           onCreateMenu : onNaviMapMenuCreation, onCreatePrerequisiteMenu : onNaviMapPrerequisiteMenuCreation, onDeleting : onMapCardDeleting}
                        , null, currentCard, currentTopic.author != me.page.server.getCurrentUser());
                        if(currentTopic.author != me.page.server.getCurrentUser()) {
                           $(document.createElement("span")).text(lang.AUTHOR).appendTo(currentMap.toolbar);
                           $(document.createElement("span")).text(currentTopic.author.name).css("font-weight", "bold").appendTo(currentMap.toolbar);
                           }
                        var toolbar = utility.createToolbar().appendTo(currentMap.toolbar.parent());
                        if(currentTopic.isEditable()) {
                           utility.createMenuItem("dialog-apply.png", "closeMapView", lang.SAVE, closeMapHandler).attr("accesskey", "o").addClass("right").appendTo(toolbar);
                           }
                        utility.createMenuItem("process-stop.png", "cancelMapView", lang.CANCEL, closeMapHandler).addClass("right").appendTo(toolbar);
                        utility.createMenuItem("map-view.png", null, lang.MANAGE_TOPICS, manageMapsHandler).appendTo(toolbar).attr("title", lang.SEARCH_MY_TOPICS_HEADER);
                        if(currentTopic.access & client.data.TopicAccessConstants.allowRemovingCards) {
                           utility.createMenuItem("edit-clear.png", null, lang.CLEANUP_TOPIC, cleanupTopicHandler).appendTo(toolbar).attr("title", lang.CLEANUP_TOPIC_EXPLANATION);
                           }
                        }
                     function manageMapsHandler(event) {
                        views.search.handleButtonClick();
                        }
                     function cleanupTopicHandler(event) {
                        currentMap.map.cleanup();
                        }
                     function createTopicHandler(event) {
                        $(this).remove();
                        createTopic();
                        }
                     function createTopic(title) {
                        var title = (title !== undefined && title != null && title.toString().length > 0) ? title.toString() : lang.UNTITLED;
                        var topic = new client.data.Topic();
                        for(var i = 0; i < INITIAL_CARD_COUNT; i++) {
                           var card = new client.data.Card();
                           card.title = lang.TYPE_CARD + " " + (i + 1);
                           var step = new client.data.Step();
                           step.addCard(client.data.LevelConstants.main, card);
                           topic.addStep(step);
                           }
                        topic.title = title;
                        afterUpload = {
                           data : null, callback : function(data, topic) {
                              me.openTopic(topic, true);
                              }
                           };
                        updateTopic(topic, topic.transformStatic());
                        }
                     this.createTopic = createTopic;
                     function closeMapHandler(event) {
                        if(event.data.action === undefined || event.data.action != "cancelMapView") {
                           updateTopicChanges();
                           }
                        currentToolCallback = function() {
                           };
                        hideView();
                        }
                     function openFromProgressBox(card) {
                        if(currentCard !== card) {
                           me.openCard(card, card.step.topic.isEditable());
                           }
                        }
                     var progressBox;
                     function updateProgress(forceReload) {
                        if(forceReload || progressBox === undefined || (progressBox.getTopics().length != 1 || progressBox.getTopics()[0] !== currentTopic)) {
                           if(progressBox) {
                              progressBox.destroy();
                              }
                           desktop.progressBox.empty();
                           $(document.createElement("h3")).addClass("progress-overview").text(lang.PROGRESS_HEADER).appendTo(desktop.progressBox);
                           progressBox = new client.widgets.ProgressBox( {
                              node : desktop.progressBox, topics : currentTopic ? [currentTopic] : [], isEditor : display.isEditor(), openCardCallback : utility.createCallback(me, openFromProgressBox), additionalWidth : 8}
                           );
                           var actionLinks = [];
                           var dealtTopics = [currentTopic];
                           lastActions.each(function() {
                              if(this.tool == client.pageplugins.MainViewToolConstants.none && this.relation !== currentCard && dealtTopics.indexOf(this.relation.step.topic) ==- 1) {
                                 dealtTopics.push(this.relation.step.topic); if(actionLinks.length < MAX_HISTORY_DISPLAY_COUNT) {
                                    var link = utility.createLink(this.relation.step.topic.title).attr("title", lang.GO_BACK_TO + " " + this.relation.title); link.bind("click", this.relation, function(event) {
                                       me.openCard(event.data); }
                                    ); actionLinks.push(link); }
                                 }
                              }
                           );
                           delete progressBox.historyPanel;
                           if(actionLinks.length > 0) {
                              progressBox.historyPanel = $(document.createElement("div")).appendTo(desktop.progressBox);
                              var historyHeader = $(document.createElement("h3")).addClass("progress-overview").text(lang.HISTORY).appendTo(progressBox.historyPanel).makeCollapsible();
                              progressBox.historyPanel.css( {
                                 "border-width" : "1px", "border-color" : "#999", "border-style" : "solid none"}
                              );
                              var historyList = $(document.createElement("div")).addClass("item-cont").css("overflow", "hidden").hide().appendTo(progressBox.historyPanel);
                              actionLinks.each(function() {
                                 var img = $(document.createElement("img")).css( {
                                    "margin-right" : 4, "vertical-align" : "middle"}
                                 ).attr("src", config.imagePath + "applications-office-small.png"); $(document.createElement("div")).css( {
                                    "margin" : "4px 0px", "white-space" : "nowrap"}
                                 ).append(img).append(this).prependTo(historyList); }
                              )historyHeader.triggerHandler("click");
                              }
                           $(document.createElement("div")).css( {
                              "height" : "20px"}
                           ).appendTo(desktop.progressBox);
                           var rDiv = $(document.createElement("div")).css( {
                              "position" : "absolute", "right" : "4px", "bottom" : "4px"}
                           ).appendTo(desktop.progressBox);
                           utility.createLink(lang.ADJUST_MORE).attr("title", lang.ADJUST_EXPLANATION).appendTo(rDiv).addClass("more margin").bind("click", mapViewHandler);
                           utility.createLink(lang.NEW_MORE).css("margin-right", 8).attr("title", lang.NEW_TOPIC_EXPLANATION).appendTo(rDiv).addClass("more margin").bind("click", createTopicHandler);
                           }
                        desktop.tabLevelAdvanced.add(desktop.tabLevelIllustrative).add(desktop.tabLevelMain).removeClass("active");
                        if(!currentCard) {
                           return;
                           }
                        var currentLevel = currentCard.step.getLevelOfCard(currentCard);
                        var activeLevel;
                        if(currentLevel == client.data.LevelConstants.main) {
                           activeLevel = desktop.tabLevelMain.addClass("active");
                           }
                        else if(currentLevel == client.data.LevelConstants.advanced) {
                           activeLevel = desktop.tabLevelAdvanced.addClass("active");
                           }
                        else if(currentLevel == client.data.LevelConstants.illustrative) {
                           activeLevel = desktop.tabLevelIllustrative.addClass("active");
                           }
                        var advancedCount = currentCard.step.getCardCount(client.data.LevelConstants.advanced);
                        var illustrativeCount = currentCard.step.getCardCount(client.data.LevelConstants.illustrative);
                        var editable = currentCard.step.topic.isEditable();
                        desktop.tabLevelAdvanced.attr("title", (editable && advancedCount == 0) ? lang.CREATE_ADVANCED : lang.SWITCH_ADVANCED);
                        desktop.tabLevelMain.attr("title", lang.SWITCH_MAIN);
                        desktop.tabLevelIllustrative.attr("title", (editable && illustrativeCount == 0) ? lang.CREATE_ILLUSTATIVE : lang.SWITCH_ILLUSTATIVE);
                        activeLevel.removeAttr("title");
                        var stepWithContentCount = 0;
                        currentTopic.each(function() {
                           var mc = this.getMainCard(); if(mc.content != null || mc.needsLoading) {
                              stepWithContentCount++; }
                           }
                        );
                        var showUpper = false;
                        var showLower = false;
                        if(editable &&!config.reduceFeatureSet && stepWithContentCount >= MIN_STEPS_FOR_LEVEL_HANDLERS && currentCard.step.getMainCard().content != null) {
                           showUpper = true;
                           showLower = true;
                           $.fn[advancedCount == 0 ? "addClass" : "removeClass"].call(desktop.tabLevelAdvanced, "grey");
                           $.fn[illustrativeCount == 0 ? "addClass" : "removeClass"].call(desktop.tabLevelIllustrative, "grey");
                           if(currentTool == client.pageplugins.MainViewToolConstants.none || researchMode) {
                              desktop.tabLevelAdvanced.add(desktop.tabLevelIllustrative).fadeIn();
                              }
                           }
                        else {
                           showUpper = advancedCount > 0;
                           showLower = illustrativeCount > 0;
                           if(currentTool == client.pageplugins.MainViewToolConstants.none || researchMode) {
                              $.fn[advancedCount == 0 ? "fadeOut" : "fadeIn"].call(desktop.tabLevelAdvanced);
                              $.fn[illustrativeCount == 0 ? "fadeOut" : "fadeIn"].call(desktop.tabLevelIllustrative);
                              }
                           }
                        if(desktop.tabLevelAdvanced.hasClass("ignore") == showUpper) {
                           desktop.tabLevelAdvanced.toggleClass("ignore");
                           }
                        if(desktop.tabLevelIllustrative.hasClass("ignore") == showLower) {
                           desktop.tabLevelIllustrative.toggleClass("ignore");
                           }
                        var currentIndex = currentTopic.getIndexOfStep(currentCard.step);
                        var isLastMain = (currentTopic.getStepCount() - 1 == currentIndex) && (currentLevel == client.data.LevelConstants.main);
                        var showRight =!isLastMain || currentTopic.isEditable();
                        if(isLastMain != desktop.buttonRight.hasClass("add")) {
                           desktop.buttonRight.toggleClass("add");
                           desktop.buttonRight.attr("title", isLastMain ? lang.NAVI_RIGHT_ADD_EXPLANATION : lang.NAVI_RIGHT_EXPLANATION);
                           }
                        if(showRight == desktop.buttonRight.hasClass("ignore")) {
                           desktop.buttonRight.toggleClass("ignore");
                           if(currentTool == client.pageplugins.MainViewToolConstants.none || researchMode) {
                              $.fn[showRight ? "fadeIn" : "fadeOut"].call(desktop.buttonRight);
                              }
                           }
                        if(currentIndex > 0 == desktop.buttonLeft.hasClass("ignore")) {
                           desktop.buttonLeft.toggleClass("ignore");
                           if(currentTool == client.pageplugins.MainViewToolConstants.none || researchMode) {
                              $.fn[currentIndex > 0 ? "fadeIn" : "fadeOut"].call(desktop.buttonLeft);
                              }
                           }
                        progressBox.unload();
                        progressBox.load( {
                           selectedCard : currentCard}
                        );
                        if(progressBox.historyPanel !== undefined) {
                           var maxWidth = progressBox.getContainer().css("width");
                           var minWidth = progressBox.getContainer().css("min-width");
                           if(parseInt(minWidth) > parseInt(maxWidth)) {
                              maxWidth = minWidth;
                              }
                           $("div.item-cont", progressBox.historyPanel).css("max-width", maxWidth);
                           }
                        }
                     function insertPage() {
                        var index = currentTopic.getIndexOfStep(currentCard.step);
                        index += 1;
                        insertCard(currentTopic, new client.data.Card(), index, client.data.LevelConstants.main);
                        }
                     var insertingCard;
                     function insertCard(topic, card, index, level) {
                        if(insertingCard) {
                           return false;
                           }
                        var steps = topic.getSteps();
                        var step;
                        if(level == client.data.LevelConstants.main) {
                           step = new client.data.Step();
                           steps.splice(index, 0, step);
                           }
                        else {
                           step = topic.getStep(index);
                           if(card.title == lang.UNTITLED) {
                              var prefix;
                              if(level == client.data.LevelConstants.advanced) {
                                 prefix = lang.MORE_DETAILS_ON;
                                 }
                              else {
                                 prefix = lang.ILLUSTRATION_OF;
                                 }
                              var mainCard = step.getMainCard();
                              var title = mainCard.title;
                              if(currentCard == mainCard) {
                                 if(display.hasChanged()) {
                                    var changes = display.getChanges();
                                    if(changes.title !== undefined) {
                                       title = changes.title;
                                       }
                                    }
                                 }
                              card.title = prefix + title;
                              }
                           }
                        step.addCard(level, card);
                        var obj = {
                           };
                        obj.cards = [];
                        for(var i = 0; i < steps.length; i++) {
                           steps[i].transformStatic().each(function() {
                              this.index = i; obj.cards.push(this); }
                           );
                           }
                        insertingCard = true;
                        return updateTopicChanges(obj, topic, {
                           index : index, level : level, isEditor : topic.isEditable()}
                        , function(data, topic) {
                           var step = topic.getStep(data.index); if(step == null) {
                              step = topic.getStep(0); if(step == null) {
                                 return; }
                              }
                           var card = step.getFirstCard(level); if(card == null) {
                              card = step.getMainCard(); }
                           me.openCard(card, data.isEditor); }
                        );
                        }
                     function naviHandler(event) {
                        var link = $(this);
                        if(link.hasClass("disabled") || currentCard == null) {
                           return;
                           }
                        var isLeft =!event.data;
                        var currentLevel = currentCard.step.getLevelOfCard(currentCard);
                        if(currentLevel == client.data.LevelConstants.main) {
                           var currentIndex = currentTopic.getIndexOfStep(currentCard.step);
                           currentIndex = currentIndex + (isLeft ?- 1 : 1);
                           var newStep = currentTopic.getStep(currentIndex);
                           if(newStep) {
                              me.openCard(newStep.getMainCard(), currentCard.step.topic.isEditable());
                              }
                           else if(!isLeft) {
                              if(currentTopic.isEditable() && currentCard.step.topic.getStepCount() < config.CARD_LIMITS[0]) {
                                 insertPage();
                                 }
                              else {
                                 alert(lang.MAX_NO_OF_CARDS_IN_TOPIC);
                                 }
                              }
                           }
                        else {
                           me.openCard(currentCard.step.getMainCard(), currentCard.step.topic.isEditable());
                           }
                        }
                     var lastActions = [];
                     var actionCount = 0;
                     function addAction(action) {
                        var actionsToRemove = [];
                        lastActions.each(function() {
                           if(this.tool == action.tool && this.relation == action.relation) {
                              actionsToRemove.push(this); }
                           }
                        );
                        actionsToRemove.each(function() {
                           lastActions.remove(this); }
                        )lastActions.unshift(action);
                        }
                     this.openTopic = function(topic, isEditor) {
                        if(topic === undefined || topic == null) {
                           topic = currentTopic;
                           }
                        if(isEditor === undefined) {
                           isEditor = topic.isEditable();
                           }
                        if(topic.getStepCount() == 0) {
                           if(!topic.isEditable()) {
                              alert(lang.TOPIC_EMPTY);
                              }
                           else {
                              insertCard(topic, new client.data.Card(), 0, client.data.LevelConstants.main);
                              desktop.progressBox.fadeIn();
                              }
                           return;
                           }
                        this.openCard(topic.getStep(0).getMainCard(), isEditor);
                        }
                     var closeAfterCallback, openAfterCallback, trainAfterCallback, switchFullAfterSave;
                     this.openCard = function(card, isEditor) {
                        topic = card.step.topic;
                        if(topic === undefined || topic == null) {
                           return;
                           }
                        if(isEditor === undefined) {
                           isEditor = topic.isEditable();
                           }
                        else if(isEditor &&!topic.isEditable()) {
                           isEditor = false;
                           }
                        openCardCoreArgs = {
                           isEditor : isEditor, topic : topic, card : card};
                        if(display && currentCard != null) {
                           display.closeCard();
                           var result;
                           result = saveCard();
                           if((result == false && card != currentCard) || card.request) {
                              pendingTimer = window.setTimeout(showPendingOverlay, 2000);
                              openAfterCallback = true;
                              return false;
                              }
                           else if(currentCard.checkedOut && (card != currentCard || isEditor != display.isEditor())) {
                              triggerCheckIn();
                              }
                           }
                        if(card.needsLoading == true) {
                           me.page.server.loadCardContents(card, utility.createCallback(me, openCardCore));
                           return false;
                           }
                        else {
                           openCardCore();
                           if(card.step.topic.isShared()) {
                              checkTopicRevision();
                              if(!isEditor) {
                                 me.checkCardRevision();
                                 }
                              }
                           return true;
                           }
                        }
                     this.saveCard = function() {
                        saveCard();
                        }
                     this.checkCardRevision = function() {
                        me.page.server.checkCardRevision(currentCard, utility.createCallback(me, revisionCheckCallback));
                        }
                     function revisionCheckCallback(data) {
                        if(data.success) {
                           if(data.cards !== undefined) {
                              display.revertChanges();
                              }
                           }
                        else {
                           alert(lang.COULD_NOT_CHECK_REVISION);
                           }
                        }
                     var openCardCoreArgs;
                     function openCardCore() {
                        var isEditor = openCardCoreArgs.isEditor;
                        var progressChanged = (currentCard !== openCardCoreArgs.card || display.isEditor() != isEditor);
                        currentCard = openCardCoreArgs.card;
                        currentTopic = currentCard.step.topic;
                        openAfterCallback = false;
                        openCardCoreArgs = null;
                        me.clearInfos();
                        hidePendingOverlay();
                        hideCheckOutOverlay();
                        display.openCard(currentCard, isEditor);
                        settings.lastCardId = currentCard.id;
                        if(currentCard.step.topic.isShared()) {
                           if(isEditor) {
                              stopCheckOut();
                              triggerCheckOut();
                              }
                           }
                        if(progressChanged) {
                           updateProgress();
                           }
                        addAction( {
                           tool : client.pageplugins.MainViewToolConstants.none, relation : currentCard}
                        );
                        preloadTopics([currentTopic]);
                        }
                     var checkOutUploadOverlay;
                     function showCheckOutOverlay() {
                        hideCheckOutOverlay();
                        checkOutUploadOverlay = createNoteOverlay();
                        checkOutUploadOverlay.css( {
                           top : 302, height : 45, "z-index" : 89}
                        );
                        $(document.createElement("p")).html(lang.CARD_LOCKED).appendTo(checkOutUploadOverlay);
                        var actionToolbar = utility.createToolbar().appendTo(checkOutUploadOverlay);
                        utility.createMenuItem("dialog-apply.png", null, lang.STAY_READ_ONLY, function() {
                           hideCheckOutOverlay(); }
                        ).appendTo(actionToolbar);
                        utility.createMenuItem("view-refresh.png", null, lang.CHECK_OUT_TRY_AGAIN, function() {
                           triggerCheckOut(); }
                        ).appendTo(actionToolbar);
                        }
                     function hideCheckOutOverlay() {
                        if(checkOutUploadOverlay !== undefined) {
                           checkOutUploadOverlay.remove();
                           checkOutUploadOverlay = undefined;
                           }
                        }
                     var checkOutTimer;
                     function triggerCheckOut(refresh) {
                        if(!currentCard.checkedOut || refresh) {
                           me.page.server.checkOutCard(currentCard, utility.createCallback(me, checkOutCallback));
                           }
                        }
                     function triggerCheckIn(card) {
                        window.clearTimeout(checkOutTimer);
                        me.page.server.checkInCard(card !== undefined ? card : currentCard, utility.createCallback(me, checkInCallback));
                        }
                     function stopCheckOut() {
                        window.clearTimeout(checkOutTimer);
                        }
                     function checkOutCallback(data) {
                        if(data.error !== undefined) {
                           showCheckOutOverlay();
                           display.closeCard();
                           display.openCard(currentCard, false);
                           me.checkCardRevision();
                           }
                        else if(data.success) {
                           hideCheckOutOverlay();
                           if(currentCard === data.card) {
                              if(!display.isEditor()) {
                                 display.closeCard();
                                 display.openCard(currentCard, true);
                                 }
                              var revision = data.revision;
                              if(revision > currentCard.revision) {
                                 me.checkCardRevision();
                                 }
                              window.clearTimeout(checkOutTimer);
                              checkOutTimer = window.setTimeout(triggerCheckOut, CHECK_OUT_INTERVAL * 1000, true);
                              }
                           else {
                              triggerCheckIn(data.card);
                              }
                           }
                        }
                     function checkInCallback(data) {
                        if(data.success) {
                           if(data.card == currentCard && display.isEditor()) {
                              triggerCheckOut();
                              }
                           }
                        else {
                           }
                        }
                     var currentTopic = null;
                     this.getTopic = function() {
                        return currentTopic == null ? undefined : currentTopic;
                        }
                     this.getCurrentTool = function() {
                        return currentTool;
                        }
                     var currentToolCallback;
                     var flashCardsToTrain;
                     var cardsToLoadBeforeTraining;
                     this.trainTopic = function(topic, subset) {
                        var cards = (subset !== undefined) ? subset : topic.getAllCards();
                        trainTopicCoreArgs = {
                           cards : cards, topic : topic};
                        if(currentCard.step.topic === topic) {
                           var result = saveCard();
                           if(!trainAfterCallback && result == false) {
                              trainAfterCallback = true;
                              return;
                              }
                           }
                        cardsToLoadBeforeTraining = [];
                        for(var i = 0; i < cards.length; i++) {
                           var card = cards[i];
                           if(card.needsLoading && card !== currentCard) {
                              cardsToLoadBeforeTraining.push(card);
                              }
                           }
                        loadCardsForTraining.call(this, null);
                        }
                     function loadCardsForTraining(data) {
                        if(data &&!data.success) {
                           alert(lang.ERROR_LOADING_TRAINING);
                           }
                        if(cardsToLoadBeforeTraining.length > 0) {
                           this.page.server.loadCardsContents(cardsToLoadBeforeTraining, utility.createCallback(this, loadCardsForTraining));
                           cardsToLoadBeforeTraining.splice(0, cardsToLoadBeforeTraining.length);
                           return false;
                           }
                        var flashCards;
                        flashCards = trainTopicCoreArgs.flashCardsToTrain = collectFlashCards(trainTopicCoreArgs.cards);
                        if(flashCards.length == 0) {
                           alert(lang.NO_FLASH_CARDS);
                           return false;
                           }
                        trainTopicCore.call(this, null);
                        return true;
                        }
                     function collectFlashCards(cards) {
                        var flashCards = [];
                        for(var i = 0; i < cards.length; i++) {
                           var card = cards[i];
                           var markers = card.getMarkers();
                           for(var j = 0; j < markers.length; j++) {
                              var marker = markers[j];
                              if(marker.trainable()) {
                                 marker.prepareTraining();
                                 if(marker.flashCards) {
                                    flashCards = flashCards.concat(marker.flashCards);
                                    }
                                 }
                              }
                           }
                        return flashCards;
                        }
                     function flashCardBoxHandler(event) {
                        if(!currentCard) {
                           return;
                           }
                        var currentTrainableCards = [];
                        var studiedTrainableCards = [];
                        var allTrainableCards = [];
                        if(display.checkTrainable()) {
                           currentTrainableCards.push(currentCard);
                           studiedTrainableCards.push(currentCard);
                           allTrainableCards.push(currentCard);
                           }
                        currentCard.step.topic.getAllCards().each(function() {
                           if(this !== currentCard) {
                              if(client.Marker.checkTrainable(this.getMarkers())) {
                                 allTrainableCards.push(this); if(this.studied) {
                                    studiedTrainableCards.push(this); }
                                 }
                              }
                           }
                        );
                        var showBalloon = false;
                        if(allTrainableCards.length == 0) {
                           hideBalloon();
                           if(explainNoTags() == null) {
                              alert(lang.INSERT_TRAINABLE_MARKERS);
                              }
                           }
                        else if(studiedTrainableCards.length == 0) {
                           hideBalloon();
                           if(explainNoTagsHere() == null) {
                              showBalloon = true;
                              }
                           else {
                              var link = $("a.mem:first", currentBalloon);
                              link.bind("click", allTrainableCards, function(event) {
                                 hideBalloon(); me.trainTopic(event.data[0].step.topic, event.data); }
                              );
                              }
                           }
                        else {
                           showBalloon = true;
                           }
                        if(showBalloon) {
                           explainScope().css("width", 220);
                           var actionToolbar = utility.createToolbar().insertBefore($("div.options:first", currentBalloon));
                           utility.createMenuItem("train.png", null, lang.MEMORIZE_ALL, trainHandler, {
                              topic : allTrainableCards[0].step.topic, cards : allTrainableCards}
                           ).appendTo(actionToolbar);
                           if(studiedTrainableCards.length > 0) {
                              utility.createMenuItem("train-studied.png", null, lang.MEMORIZE_STUDIED, trainHandler, {
                                 topic : studiedTrainableCards[0].step.topic, cards : studiedTrainableCards}
                              ).appendTo(actionToolbar);
                              };
                           if(currentTrainableCards.length > 0) {
                              utility.createMenuItem("train-studied.png", null, lang.MEMORIZE_CURRENT, trainHandler, {
                                 topic : currentTrainableCards[0].step.topic, cards : currentTrainableCards}
                              ).appendTo(actionToolbar);
                              };
                           }
                        }
                     function trainHandler(event) {
                        hideBalloon();
                        me.trainTopic(event.data.topic, event.data.cards);
                        }
                     function hideBalloon() {
                        if(currentBalloon) {
                           currentBalloon.remove();
                           currentBalloon = null;
                           }
                        }
                     function explainNoTags(after) {
                        hideBalloon();
                        currentBalloon = $.showBalloon(desktop.flashCardBox, lang.BALLOON_TEXTS.DESKTOP.NO_TAGS_EXPLANATION, jQuery.balloon.BalloonPositionConstants.topLeft, undefined, balloonConfig, "notags", after);
                        return currentBalloon;
                        }
                     function explainNoTagsHere(after) {
                        hideBalloon();
                        currentBalloon = $.showBalloon(desktop.flashCardBox, lang.BALLOON_TEXTS.DESKTOP.NO_TAGS_HERE_EXPLANATION, jQuery.balloon.BalloonPositionConstants.topLeft, undefined, balloonConfig, "notagshere", after);
                        return currentBalloon;
                        }
                     function explainScope(after) {
                        hideBalloon();
                        currentBalloon = $.showBalloon(desktop.flashCardBox, lang.BALLOON_TEXTS.DESKTOP.CHOOSE_SCOPE, jQuery.balloon.BalloonPositionConstants.topLeft, undefined, undefined, undefined, after);
                        return currentBalloon;
                        }
                     var trainTopicCoreArgs;
                     function trainTopicCore() {
                        hideView();
                        currentTopic = trainTopicCoreArgs.topic;
                        currentFlashCards = trainTopicCoreArgs.flashCardsToTrain;
                        for(var i = 0; i < currentFlashCards.length; i++) {
                           this.page.server.addFlashCard(currentFlashCards[i]);
                           }
                        var container = me.requestContainer(client.pageplugins.MainViewToolConstants.trainer, undefined, true);
                        trainer = new client.pageplugins.CardTrainer( {
                           container : container}
                        );
                        this.page.loadPagePlugin(trainer);
                        me.clearInfos();
                        addAction( {
                           tool : currentTool, relation : currentTopic}
                        );
                        trainer.trainCards(currentFlashCards.clone());
                        trainTopicCoreArgs = null;
                        }
                     function saveCard() {
                        if(display.hasChanged()) {
                           var callback = utility.createCallback(me, updateCallback);
                           display.saveCard();
                           var request;
                           if(display.isEditor()) {
                              if(currentCard.checkedOut ||!currentCard.step.topic.isShared()) {
                                 request = me.page.server.updateCard(currentCard, display.getChanges(), callback);
                                 }
                              }
                           else {
                              var changes = display.getChanges();
                              request = me.page.server.updateMarkers(currentCard, changes.markers, changes.deletedMarkerIds, callback);
                              }
                           display.resetChanges();
                           return request !== undefined && request.readyState == 4 && request.status == 200;
                           }
                        else {
                           return true;
                           }
                        }
                     function hideOverlays() {
                        hideIntroOverlay();
                        hidePendingOverlay();
                        hideCheckOutOverlay();
                        }
                     var introOverlay;
                     function showIntroOverlay(helperFunction) {
                        hideIntroOverlay();
                        introOverlay = createNoteOverlay();
                        introOverlay.css( {
                           top : 200, height : 147, "z-index" : 85}
                        )helperFunction(introOverlay, me);
                        }
                     function hideIntroOverlay() {
                        if(introOverlay !== undefined) {
                           introOverlay.remove();
                           introOverlay = undefined;
                           }
                        }
                     this.hideIntro = hideIntroOverlay;
                     var feedbackOverlay;
                     function showFeedbackOverlay() {
                        hideFeedbackOverlay();
                        feedbackOverlay = createNoteOverlay();
                        feedbackOverlay.css( {
                           top : 200, height : 147}
                        )var content = $(document.createElement("div"));
                        feedbackOverlay.textarea = $(document.createElement("textarea")).css( {
                           height : 84, width : "100%"}
                        ).appendTo(content);
                        var panel = utility.createPanelDialog(content, {
                           }
                        , feedbackDialogHandler, lang.SEND_FEEDBACK, lang.SEND).children().appendTo(feedbackOverlay);
                        panel.focusFirstEditable();
                        }
                     function feedbackDialogHandler(event) {
                        if(event.data.action == "ok") {
                           var message = feedbackOverlay.textarea.val();
                           if(message.length > 0) {
                              me.page.server.suggestFeature(message, utility.createCallback(me, function(data) {
                                 if(data.success) {
                                    me.queueInfo(lang.THANKS_FEEDBACK); hideFeedbackOverlay(); }
                                 else {
                                    alert(lang.COULD_NOT_SEND_FEEDBACK); }
                                 }
                              ))}
                           else {
                              alert(lang.MESSAGE_EMPTY);
                              }
                           }
                        else {
                           hideFeedbackOverlay();
                           }
                        }
                     function hideFeedbackOverlay() {
                        if(feedbackOverlay !== undefined) {
                           feedbackOverlay.remove();
                           feedbackOverlay = undefined;
                           }
                        }
                     function createNoteOverlay(hide) {
                        var overlay = $(document.createElement("div")).addClass("full-container layer").click(function(event) {
                           event.stopPropagation(); }
                        );
                        if(hide) {
                           overlay.hide();
                           }
                        overlay.appendTo(desktop);
                        return overlay;
                        }
                     var pendingUploadOverlay, pendingTimer;
                     function showPendingOverlay() {
                        hidePendingOverlay();
                        pendingUploadOverlay = createNoteOverlay();
                        var header = $(document.createElement("h2")).text(lang.CARD_UPDATE_PENDING).appendTo(pendingUploadOverlay);
                        $(document.createElement("div")).addClass("loader show").insertBefore(header);
                        var actionToolbar = utility.createToolbar().appendTo(pendingUploadOverlay);
                        utility.createMenuItem("process-stop.png", null, lang.ABORT_UPLOAD, abortUpdate).appendTo(actionToolbar);
                        }
                     function abortUpdate() {
                        me.page.server.abortUpdate(openCardCoreArgs.card);
                        openCardCore();
                        }
                     function hidePendingOverlay() {
                        if(pendingTimer !== undefined) {
                           window.clearTimeout(pendingTimer);
                           pendingTimer = undefined;
                           }
                        if(pendingUploadOverlay !== undefined) {
                           pendingUploadOverlay.remove();
                           pendingUploadOverlay = undefined;
                           }
                        }
                     function updateCallback(data) {
                        if(data.error !== undefined) {
                           askForRetry(data.tag, data.error);
                           }
                        else {
                           if(switchFullAfterSave) {
                              var href = desktop.flashCardBox.attr("href");
                              var target = desktop.flashCardBox.attr("target");
                              if(target == "_parent") {
                                 window.parent.location = href;
                                 }
                              else if(target == "_self") {
                                 window.location = href;
                                 }
                              else {
                                 window.frames[target].location = href;
                                 }
                              return;
                              }
                           if(!closeAfterCallback &&!openAfterCallback && data.card === currentCard) {
                              display.revertChanges();
                              updateProgress();
                              }
                           if(openAfterCallback) {
                              openCardCore();
                              openAfterCallback = false;
                              }
                           if(trainAfterCallback) {
                              me.trainTopic(trainTopicCoreArgs.topic);
                              trainAfterCallback = false;
                              }
                           if(closeAfterCallback) {
                              closeSession();
                              closeAfterCallback = false;
                              }
                           }
                        }
                     function askForRetry(card) {
                        var overlay = createNoteOverlay();
                        $(document.createElement("h2")).text(lang.CARD_UPDATE_FAILURE.replace("####", '"' + card.title + '"')).appendTo(overlay);
                        var actionToolbar = utility.createToolbar().appendTo(overlay);
                        utility.createMenuItem("process-stop.png", null, lang.DISMISS_DATA, function(event) {
                           event.data.overlay.remove(); if(currentCard === event.data.card) {
                              display.revertChanges(); }
                           }
                        , {
                           card : card, overlay : overlay}
                        ).appendTo(actionToolbar);
                        utility.createMenuItem("go-next.png", null, lang.RETRY_UPLOAD, function(event) {
                           me.page.server.resendUpdateRequest(event.data.card, event.data.data, utility.createCallback(me, updateCallback))event.data.overlay.remove(); }
                        , {
                           data : card.requestData, card : card, overlay : overlay}
                        ).appendTo(actionToolbar);
                        }
                     function saveTrainingSession() {
                        var concernedMarkers = getMarkersFromFlashCards(currentFlashCards);
                        me.page.server.updateTrainingSession(concernedMarkers, utility.createCallback(me, trainingSessionCallback));
                        return false;
                        }
                     function trainingSessionCallback(data) {
                        if(data.error) {
                           alert(lang.COULD_NOT_SYNC_TRAINING);
                           }
                        if(closeAfterCallback) {
                           closeSession();
                           }
                        }
                     function prepareFullContainer() {
                        desktop.fullContainer.empty();
                        var closeButton = utility.createLink().addClass("close-button").bind("click", hideView).appendTo(desktop.fullContainer);
                        desktop.previewContainer.fadeOut();
                        window.focus();
                        desktop.fullContainer.show().focusFirstEditable();
                        }
                     this.hideView = function() {
                        hideView();
                        }
                     var alertQueue, alertBalloon;
                     this.queueInfo = function(html) {
                        if(alertQueue === undefined) {
                           alertQueue = [];
                           desktop.alertInfo.bind("click", function(event) {
                              event.stopPropagation(); var item = alertQueue.shift(); if(item) {
                                 alertBalloon = jQuery.showBalloon($(this), item, jQuery.balloon.BalloonPositionConstants.topMiddle, function() {
                                    if(alertQueue.length == 0) {
                                       desktop.alertInfo.fadeOut(); }
                                    }
                                 ); }
                              }
                           );
                           }
                        var newItem = $(document.createElement("div"));
                        alertQueue.push(newItem.html(html));
                        desktop.alertInfo.fadeIn();
                        return newItem;
                        }
                     this.clearInfos = function() {
                        alertQueue = undefined;
                        desktop.alertInfo.fadeOut();
                        if(alertBalloon) {
                           alertBalloon.remove();
                           alertBalloon = undefined;
                           }
                        }
                     function shoutboxLinkHandler(event) {
                        if(feedbackOverlay === undefined) {
                           showFeedbackOverlay();
                           }
                        }
                     function hideView(leaveTools) {
                        var state = true;
                        hideFloat();
                        if(currentTool == client.pageplugins.MainViewToolConstants.mapView) {
                           currentToolCallback();
                           client.widgets.MapView.destroyMap(currentMap);
                           currentMap = undefined;
                           desktop.fullContainer.empty().hide();
                           currentToolCallback = undefined;
                           }
                        else if(currentTool == client.pageplugins.MainViewToolConstants.trainer) {
                           var flashCard = trainer.getCurrentFlashCard();
                           if(flashCard) {
                              var card = flashCard.marker.card;
                              me.openCard(card, card.step.topic.isEditable());
                              }
                           saveTrainingSession();
                           me.page.unloadPagePlugin(trainer);
                           desktop.fullContainer.empty().hide();
                           }
                        else if(currentToolCallback !== undefined) {
                           currentToolCallback();
                           desktop.fullContainer.empty().hide();
                           currentToolCallback = undefined;
                           }
                        currentTool = client.pageplugins.MainViewToolConstants.none;
                        if(leaveTools !== true) {
                           showTools(true);
                           }
                        return state;
                        }
                     function showTools(show) {
                        display.showMarkers(show);
                        var selection = desktop.cardTools.not(".ignore");
                        if(show) {
                           if(!currentTopic.isEditable()) {
                              if(currentCard.step.getFirstCard(client.data.LevelConstants.advanced) == null) {
                                 selection = selection.not(desktop.tabLevelAdvanced);
                                 }
                              if(currentCard.step.getFirstCard(client.data.LevelConstants.illustrative) == null) {
                                 selection = selection.not(desktop.tabLevelIllustrative);
                                 }
                              }
                           selection.fadeIn("slow");
                           }
                        else {
                           desktop.progressBox.fadeOut();
                           desktop.cardTools.fadeOut("slow");
                           }
                        }
                     function getMarkersFromFlashCards(flashCards) {
                        var markers = [];
                        for(var i = 0; i < flashCards.length; i++) {
                           var flashCard = flashCards[i];
                           if(markers.indexOf(flashCard.marker) ==- 1) {
                              markers.push(flashCard.marker);
                              }
                           }
                        return markers;
                        }
                     function updateTopic(topic, data) {
                        me.page.server.updateTopic(topic, data, utility.createCallback(me, updateTopicCallback));
                        desktop.progressBox.fadeIn();
                        }
                     function updateTopicChanges(data, topic, tag, callback) {
                        var needsUpdate = true;
                        if(data === undefined) {
                           var map = currentMap;
                           if((map === undefined) || map == null) {
                              return;
                              }
                           data = $.extend(map.map.getChanges(), map.changes);
                           needsUpdate = map.isEditor && (map.changed || map.map.hasChanged());
                           topic = map.topic;
                           }
                        if(needsUpdate) {
                           afterUpload = {
                              data : tag, callback : callback};
                           return updateTopic(topic, data);
                           }
                        else {
                           if(callback !== undefined) {
                              callback.apply(me, [tag, topic]);
                              }
                           }
                        }
                     var afterUpload;
                     function updateTopicCallback(data) {
                        insertingCard = false;
                        var afterUploadCopy = afterUpload;
                        afterUpload = undefined;
                        if(data.success) {
                           if(data.reverted) {
                              alert(lang.TOPIC_COULD_NOT_BE_UPDATED);
                              }
                           if(afterUploadCopy !== undefined && afterUploadCopy.callback !== undefined) {
                              afterUploadCopy.callback.apply(me, [afterUploadCopy.data, data.topic]);
                              }
                           }
                        else {
                           alert(lang.COULD_NOT_UPDATE_TOPIC);
                           }
                        }
                     this.closeSession = function() {
                        logoutHandler();
                        }
                     function closeSession(data) {
                        me.page.server.closeSession(utility.createCallback(this, sessionClosed));
                        }
                     function sessionClosed(data) {
                        if(data.success) {
                           utility.triggerCallback(args.sessionEndCallback, null);
                           }
                        else {
                           alert(lang.COULD_NOT_CLOSE_SESSION);
                           }
                        }
                     function onNaviMapMenuCreation(fargs) {
                        var topic = fargs.topic;
                        var card = fargs.card;
                        var menu = fargs.menu;
                        var isEditor = fargs.isEditor;
                        if(isEditor && card == currentCard) {
                           return;
                           }
                        if(card.id == null || card.step == null) {
                           return;
                           }
                        var editButton;
                        if(topic.isEditable()) {
                           editButton = utility.createMenuItem("editor.png", null, (card.content || card.needsLoading) ? lang.EDIT_CARD_FROM_MAP : lang.EDIT_EMPTY_CARD_FROM_MAP, openFromMap, $.extend($.extend( {
                              }
                           , fargs), {
                              isEditor : true}
                           )).appendTo(menu);
                           }
                        if(card.content || card.needsLoading) {
                           utility.createMenuItem("study-tool.png", null, lang.STUDY_CARD_FROM_MAP, openFromMap, $.extend($.extend( {
                              }
                           , fargs), {
                              isEditor : false}
                           )).appendTo(menu);
                           }
                        if(editButton &&!isEditor && (card.content || card.needsLoading)) {
                           editButton.next().after(editButton);
                           }
                        }
                     function onNaviMapPrerequisiteMenuCreation(fargs) {
                        var topic = fargs.prerequisite.topic;
                        var isEditor = fargs.isEditor;
                        var menu = fargs.menu;
                        if(topic) {
                           utility.createMenuItem(isEditor ? "editor.png" : "study-tool.png", "", isEditor ? lang.EDIT_FIRST_CARD_OF_PREREQUISITE : lang.STUDY_PREREQUISITE, openFromMap, fargs).appendTo(menu);
                           }
                        }
                     function onMapCardDeleting(event) {
                        var card = event.card;
                        if((currentCard.step == card.step && card.step.getLevelOfCard(card) == client.data.LevelConstants.main) || currentCard == card) {
                           if(event.isCleanup === undefined) {
                              alert(event.isLast ? lang.CANNOT_DELETE_LAST : lang.CANNOT_DELETE_WHILE_OPEN);
                              }
                           utility.cancelEvent(event);
                           }
                        }
                     function openFromMap(event) {
                        var fargs = event.data;
                        var topic;
                        var isEditor = fargs.isEditor;
                        if(fargs.prerequisite) {
                           if((topic = fargs.prerequisite.topic)) {
                              me.openTopic(topic, isEditor);
                              me.hideView();
                              }
                           }
                        else {
                           topic = fargs.topic;
                           var card = fargs.card ? fargs.card : currentCard;
                           if(card != currentCard) {
                              updateTopicChanges(undefined, undefined, {
                                 card : card, isEditor : isEditor}
                              , function(data, topic) {
                                 me.openCard(data.card, data.isEditor); me.hideView(); }
                              );
                              }
                           }
                        }
                     this.unload = function() {
                        window.alert = utility.alertSave;
                        this.page.server.unregisterSyncHandler("topics", topicsChangedHandler);
                        this.page.server.unregisterSyncHandler("topic", topicsChangedHandler);
                        for(var i in views) {
                           me.page.unloadPagePlugin(views[i]);
                           }
                        me.page.unloadPagePlugin(display);
                        hidePendingOverlay();
                        stopCheckOut();
                        unloadUI();
                        me = null;
                        delete utility.globalSave;
                        client.PagePlugin.prototype.unload.call(this);
                        }
                     function unloadUI() {
                        if(logoutNode !== undefined) {
                           logoutNode.remove();
                           }
                        hideView(true);
                        hideFloat(true);
                        container.remove();
                        }
                     }
                  client.pageplugins.MainView.prototype = new client.PagePlugin;
                  client.pageplugins.Community = function() {
                     client.PagePlugin.call(this);
                     var PROBLEM_CHECK_PAUSE = 10000;
                     var DIALOG_WIDTH = 300;
                     var DIALOG_HEIGHT = 140;
                     var USER_ICON = "user-small.png";
                     var TOPIC_ICON = "applications-office-small.png";
                     var me;
                     this.load = function() {
                        me = this;
                        this.page.server.registerSyncHandler("card", cardChangedHandler);
                        this.page.server.registerSyncHandler("groups", groupsChangedHandler);
                        this.page.server.registerSyncHandler("group", groupsChangedHandler);
                        this.page.server.registerSyncHandler("topics", topicsChangedHandler);
                        this.page.server.registerSyncHandler("topic", topicsChangedHandler);
                        };
                     this.unload = function() {
                        me = undefined;
                        this.page.server.unregisterSyncHandler("card", cardChangedHandler);
                        this.page.server.unregisterSyncHandler("groups", groupsChangedHandler);
                        this.page.server.unregisterSyncHandler("group", groupsChangedHandler);
                        this.page.server.unregisterSyncHandler("topics", topicsChangedHandler);
                        this.page.server.unregisterSyncHandler("topic", topicsChangedHandler);
                        };
                     function cardChangedHandler() {
                        updateProblemList();
                        updatePreview();
                        }
                     function topicsChangedHandler() {
                        updateGroupRightsList();
                        }
                     function groupsChangedHandler() {
                        updatePreview();
                        updateProblemList();
                        updateGroupList();
                        updateGroupRightsList();
                        updateEditor();
                        }
                     var mainView;
                     this.onPluginLoad = function(plugin) {
                        if(plugin instanceof client.pageplugins.MainView) {
                           mainView = plugin;
                           }
                        };
                     this.onPluginRemove = function(plugin) {
                        if(plugin === mainView) {
                           delete mainView;
                           }
                        };
                     function getPendingItems() {
                        var groups = me.page.server.getGroups();
                        var currentUser = me.page.server.getCurrentUser();
                        var ownMembership;
                        var currentUserIsAdmins;
                        var invitations = [];
                        var ownRequests = [];
                        var othersRequests = [];
                        for(var i = 0; i < groups.length; i++) {
                           groups[i].getMembers().each(function() {
                              ownMembership = currentUser.getMembershipInGroup(groups[i]); currentUserIsAdmin = ownMembership != null && (ownMembership.level & client.data.MembershipLevelConstants.admin) == client.data.MembershipLevelConstants.admin; if(this.user == currentUser && this.enabled >= client.data.MembershipStatusConstants.invitedByAdmin) {
                                 invitations.push(this); }
                              else if(this.user == currentUser && this.enabled >= client.data.MembershipStatusConstants.invited) {
                                 invitations.push(this); }
                              else if(this.user == currentUser && this.enabled == client.data.MembershipStatusConstants.requested) {
                                 ownRequests.push(this); }
                              else if(this.user != currentUser && currentUserIsAdmin && this.enabled == client.data.MembershipStatusConstants.requested) {
                                 othersRequests.push(this); }
                              }
                           );
                           }
                        return[invitations, ownRequests, othersRequests];
                        }
                     var preview;
                     this.getPreview = function() {
                        if(preview === undefined) {
                           preview = $(document.createElement("div"));
                           }
                        updatePreview();
                        if(refreshProblems()) {
                           refreshGroups();
                           refreshTopics();
                           }
                        return preview;
                        }
                     var problemPreviewList;
                     function updatePreview() {
                        if(preview === undefined) {
                           return;
                           }
                        var content = preview.empty();
                        if(mainView !== undefined) {
                           var pendingItems = getPendingItems();
                           var labels = [lang.OPEN_INVITATIONS_HTML, lang.OPEN_REQUESTS_HTML, lang.OPEN_OTHERS_REQUESTS_HTML];
                           var previewNewsHeading = $(document.createElement("h3")).text(lang.NOTIFICATIONS).appendTo(content);
                           problemPreviewList = $(document.createElement("div")).appendTo(content);
                           var anyAdded = false;
                           for(var i = 0; i < pendingItems.length; i++) {
                              var members = pendingItems[i];
                              var label = labels[i];
                              if(members.length > 0) {
                                 anyAdded = true;
                                 addNewsLink(label, members.length, content);
                                 }
                              }
                           if(!anyAdded) {
                              previewNewsHeading.hide();
                              }
                           updateProblemList();
                           $(document.createElement("h3")).text(lang.FIND_FRIENDS_HEADER).appendTo(content);
                           var toolbar = utility.createToolbar().addClass("noclear");
                           $(document.createElement("img")).attr("src", config.imagePath + "group-small.png").addClass("left icon small").appendTo(toolbar);
                           var searchField = utility.createInput(lang.SEARCH);
                           var searchGroupButton = utility.createMenuItem("go-next.png", null, lang.GO);
                           searchGroupButton.bind("click", searchField, searchGoHandler);
                           searchField.bind("keypress", searchField, searchGoHandler);
                           groupsLink = utility.createLink(lang.FIND_FRIENDS_MORE).appendTo(content).addClass("more margin").bind("click", function() {
                              showView(client.pageplugins.MainViewToolConstants.studentFinder); groupList.search(); }
                           );
                           toolbar.append(searchField).append(searchGroupButton).appendTo(content);
                           var topic = mainView.getTopic();
                           if(topic !== undefined) {
                              $(document.createElement("h3")).text(lang.SHARING_SUB_HEADER).appendTo(content);
                              var toolbar = utility.createToolbar().addClass("noclear");
                              var isShared = topic.isShared();
                              if(topic.access == client.data.TopicAccessConstants.owner) {
                                 toolbar.text(isShared ? lang.SHARED_WITH : lang.UNSHARED);
                                 }
                              else {
                                 toolbar.text(lang.SHARED_WITH_YOU);
                                 }
                              utility.createLink(lang.SHARING_MORE).appendTo(content).addClass("more").bind("click", client.pageplugins.MainViewToolConstants.sharingManager, showView);
                              toolbar.appendTo(content);
                              }
                           }
                        }
                     function addNewsLink(label, count, content) {
                        var panel = $(document.createElement("div")).addClass("pending-member").html(label);
                        var link = utility.createLink("" + count);
                        $("span", panel).replaceWith(link);
                        link.bind("click", client.pageplugins.MainViewToolConstants.membershipManager, showView);
                        panel.appendTo(content);
                        }
                     function searchGoHandler(event) {
                        if(event.type == "keypress" && event.keyCode != 13) {
                           return;
                           }
                        if(showView(client.pageplugins.MainViewToolConstants.studentFinder)) {
                           var field = event.data;
                           if(!field.hasClass("grey")) {
                              groupList.search(field.val());
                              }
                           }
                        }
                     var lastView;
                     this.handleButtonClick = function() {
                        var view = client.pageplugins.MainViewToolConstants.studentFinder;
                        var pendingItems = getPendingItems();
                        var pendingCount = 0;
                        pendingItems.each(function() {
                           pendingCount += this.length; }
                        );
                        if(pendingCount == 0) {
                           var problems = collectProblems();
                           pendingCount = problems.length;
                           }
                        var memberOfAnyGroup = me.page.server.memberOfAnyGroup();
                        if(pendingCount > 0) {
                           view = client.pageplugins.MainViewToolConstants.membershipManager;
                           }
                        else if(memberOfAnyGroup && mainView !== undefined) {
                           var currentTopic = mainView.getTopic();
                           if(currentTopic !== undefined &&!currentTopic.isShared() && currentTopic.access == client.data.TopicAccessConstants.owner) {
                              view = client.pageplugins.MainViewToolConstants.sharingManager;
                              }
                           }
                        var showAll = memberOfAnyGroup && (view == client.pageplugins.MainViewToolConstants.studentFinder) && (groupList === undefined);
                        var ret = showView(view);
                        if(showAll) {
                           groupList.search();
                           }
                        return ret;
                        }
                     var groupList;
                     var requestList;
                     var sharingEditor;
                     var licenseEditor;
                     function showView(view) {
                        if(typeof view == "object") {
                           view.stopPropagation();
                           view = view.data;
                           }
                        var container;
                        if(mainView !== undefined) {
                           container = mainView.requestContainer(view, closeHandler);
                           container.click(hideMenu);
                           if(!container) {
                              return false;
                              }
                           }
                        else {
                           return false;
                           }
                        var topic = mainView.getTopic();
                        var heading;
                        if(view == client.pageplugins.MainViewToolConstants.sharingManager) {
                           var isOwner = (topic.access == client.data.TopicAccessConstants.owner);
                           heading = $(document.createElement("h1"));
                           heading.text(isOwner ? lang.EDIT_SHARING_HEADER : lang.VIEW_SHARING_HEADER).appendTo(container);
                           sharingEditor = setupSharingEditor(topic);
                           container.append(sharingEditor.container);
                           licenseEditor = setupLicenseEditor(topic, isOwner);
                           updateGroupRightsList();
                           container.append(licenseEditor.container);
                           }
                        else if(view == client.pageplugins.MainViewToolConstants.studentFinder) {
                           heading = $(document.createElement("h1"));
                           heading.text(lang.GROUP_FINDER_HEADER).appendTo(container);
                           var panel = $(document.createElement("div")).appendTo(container);
                           var groupProvider = new client.widgets.MediabirdSearchProvider( {
                              defaultType : client.widgets.MediabirdSearchProviderType.group, server : me.page.server}
                           );
                           groupProvider.name = lang.GROUP;
                           groupList = new client.widgets.FilterSearch( {
                              container : panel, providers : [groupProvider], resultClickHandler : groupListClickHandler, emptyHTML : lang.NO_GROUP_RESULTS_HTML, introHTML : lang.FIND_BUDDIES_HTML}
                           );
                           $(document.createElement("h3")).text(lang.COULD_NOT_FIND_BUDDIES_QUESTION).appendTo(container);
                           var createNewGroupToolbar = utility.createToolbar().appendTo(container);
                           utility.createMenuItem("group-create.png", "finder", lang.CREATE_GROUP, createGroupHandler).appendTo(createNewGroupToolbar);
                           utility.createMenuItem("map-share.png", null, lang.SHARE_FOLDER, showView, client.pageplugins.MainViewToolConstants.sharingManager).attr("title", lang.SHARE_FOLDER_EXPLANATION).addClass("right").appendTo(createNewGroupToolbar);
                           }
                        else if(view == client.pageplugins.MainViewToolConstants.membershipManager) {
                           $(document.createElement("h1")).text(lang.NOTIFICATIONS).appendTo(container);
                           $(document.createElement("h3")).text(lang.PROBLEM_LIST_HEADING).appendTo(container);
                           var problemListContainer = $(document.createElement("div")).appendTo(container);
                           problemListContainer.css("height", 150).css("overflow-y", "auto");
                           problemList = new client.widgets.TreeView( {
                              columns : lang.PROBLEM_LIST_HEADERS, container : problemListContainer}
                           );
                           updateProblemList();
                           $(document.createElement("h3")).text(lang.REQUEST_LIST_HEADER).appendTo(container);
                           var requestListContainer = $(document.createElement("div")).appendTo(container);
                           requestListContainer.css("height", 90).css("overflow-y", "auto");
                           requestList = new client.widgets.TreeView( {
                              columns : lang.REQUESTS_LIST_HEADERS, container : requestListContainer}
                           );
                           var groupListToolbar = utility.createToolbar().appendTo(container);
                           utility.createMenuItem("group-find.png", null, lang.VIEW_MY_GROUPS, function() {
                              showView(client.pageplugins.MainViewToolConstants.studentFinder); groupList.search(); }
                           ).addClass("right").appendTo(groupListToolbar);
                           updateGroupList();
                           }
                        lastView = view;
                        return true;
                        }
                     var lastProblemRefresh;
                     function refreshProblems() {
                        var end = new Date();
                        if(lastProblemRefresh === undefined || (end.getTime() - lastProblemRefresh.getTime() > PROBLEM_CHECK_PAUSE)) {
                           me.page.server.getCardsWithMarker("question", utility.createCallback(me, getProblemsCallback));
                           lastProblemRefresh = end;
                           return true;
                           }
                        return false;
                        }
                     function getProblemsCallback(data) {
                        if(data.success) {
                           var cardsToBeLoaded = [];
                           data.cards.each(function() {
                              if(this.needsLoading) {
                                 cardsToBeLoaded.push(this); }
                              }
                           );
                           if(cardsToBeLoaded.length > 0) {
                              me.page.server.loadCardsContents(cardsToBeLoaded, utility.createCallback(me, updateProblemList));
                              }
                           else {
                              updateProblemList();
                              }
                           }
                        }
                     var problemList;
                     function updateProblemList() {
                        if(problemList === undefined && problemPreviewList === undefined) {
                           return;
                           }
                        var problems = collectProblems();
                        if(problemList !== undefined) {
                           problemList.unload();
                           var currentUser = me.page.server.getCurrentUser();
                           var items = [];
                           problems.each(function() {
                              if(this.data.question != null) {
                                 var title = $("<div>" + this.data.question + "</div>").text(); var topic = this.card.step.topic; var user = this.user; var group = undefined; topic.getRights().each(function() {
                                    if(this.access > 0 && user.getMembershipInGroup(this.group) != null) {
                                       group = this.group; return false; }
                                    }
                                 ); var link = utility.createLink(); link.bind("click", this, viewProblemHandler); if(title.length > 20) {
                                    link.attr("title", title); title = title.substr(0, 20) + '...'; }
                                 else if(title == "") {
                                    title = lang.EMPTY_PROBLEM; }
                                 link.text(title); var groupItem; if(group !== undefined) {
                                    groupItem = createGroupLink(group); }
                                 var userLabel = $(document.createElement("span")).text(currentUser === user ? "(" + lang.YOU + ")" : user.name); var state = this.data.answer != null ? lang.ANSWERED : lang.UNSOLVED; var item = new client.widgets.TreeViewItem(link, [state, userLabel, groupItem !== undefined ? groupItem : ""], [], "problem-marker-small.png"); item._order = this.data.answer != null ? 1 : 0; items.push(item); }
                              }
                           );
                           items.sort(function(a, b) {
                              return a._order - b._order; }
                           );
                           problemList.load( {
                              items : items}
                           );
                           }
                        if(problemPreviewList !== undefined && problems.length > 0) {
                           problemPreviewList.empty();
                           addNewsLink(lang.QUESTION_REQUESTS_HTML, problems.length, problemPreviewList);
                           problemPreviewList.prev("h3").show();
                           }
                        }
                     function collectProblems() {
                        var problems = [];
                        var currentUser = me.page.server.getCurrentUser();
                        var topics = me.page.server.getTopics();
                        topics.each(function() {
                           this.each(function() {
                              var cards = this.getCards(); cards.each(function() {
                                 if(this.needsLoading == false) {
                                    var markers = this.getMarkers(); markers.each(function() {
                                       if(this.tool == "question" && this.notify == true) {
                                          problems.push(this); }
                                       }
                                    ); }
                                 }
                              ); }
                           ); }
                        );
                        return problems;
                        }
                     function viewProblemHandler(event) {
                        if(mainView !== undefined) {
                           var card = event.data.card;
                           if(card != null) {
                              mainView.openCard(card, card.step.topic.isEditable());
                              mainView.hideView();
                              }
                           }
                        }
                     function groupListClickHandler(event) {
                        var obj = event.data.object;
                        event.stopPropagation();
                        var groups = me.page.server.getGroups();
                        var group;
                        for(var i = 0; i < groups.length; i++) {
                           if(groups[i].id == obj.id) {
                              group = groups[i];
                              break;
                              }
                           }
                        if(group !== undefined) {
                           editGroup(group);
                           }
                        }
                     var groupEditor;
                     function editGroup(group, createAfterClose, shareAfterCreate) {
                        if(groupEditor === undefined) {
                           hideAddShareDialog();
                           var floatObj = mainView.requestFloat(function() {
                              groupEditor = undefined; }
                           );
                           floatObj.content.bind("click", hideMenu);
                           groupEditor = {
                              floatObj : floatObj, container : floatObj.content, shareOnCreate : shareAfterCreate};
                           }
                        updateEditor(group, createAfterClose);
                        }
                     function updateEditor(group, createAfterClose) {
                        if(groupEditor === undefined) {
                           return;
                           }
                        if(group === undefined) {
                           group = groupEditor.group;
                           }
                        groupEditor.container.children().not(":first").not(":first").remove()var content = groupEditor.container;
                        var currentUser = me.page.server.getCurrentUser();
                        var ownMembership = currentUser.getMembershipInGroup(group);
                        var editable = createAfterClose || (ownMembership != null ? ((ownMembership.level & client.data.MembershipLevelConstants.admin) == client.data.MembershipLevelConstants.admin) : false);
                        $(document.createElement("h1")).text(editable ? (createAfterClose ? lang.CREATE_GROUP : lang.EDIT_GROUP_HEADER) : lang.VIEW_GROUP_HEADER).appendTo(content);
                        var groupLabel, nameField, categoryField;
                        var groupRow = utility.createToolbar().appendTo(content);
                        var nameDiv = $(document.createElement("div")).addClass("clear").appendTo(groupRow);
                        $(document.createElement("span")).text(lang.GROUP_NAME + (editable ? "" : ": " + group.name)).appendTo(nameDiv);
                        if(editable) {
                           nameField = $.createEditableLabel(undefined, undefined, undefined, 60).text(group.name).appendTo(nameDiv);
                           }
                        var categoryDiv = $(document.createElement("div")).addClass("clear").appendTo(groupRow);
                        $(document.createElement("span")).text(lang.GROUP_CATEGORY + (editable ? "" : ": " + group.category)).appendTo(groupRow);
                        if(editable) {
                           categoryField = $.createEditableLabel(undefined, undefined, undefined, 60).text(group.category).appendTo(groupRow);
                           }
                        if(createAfterClose) {
                           nameField.triggerHandler("click");
                           }
                        var accessSelector = $(document.createElement(editable ? "select" : "span")).addClass("auto");
                        if(editable) {
                           accessSelector.append($(document.createElement("option")).val(client.data.GroupAccessConstants.noAccess).text(lang.GROUP_NO_ACCESS));
                           accessSelector.append($(document.createElement("option")).val(client.data.GroupAccessConstants.allowView).text(lang.GROUP_VIEW_ACCESS));
                           accessSelector.append($(document.createElement("option")).val(client.data.GroupAccessConstants.allowJoin).text(lang.GROUP_JOIN_ACCESS));
                           accessSelector.val(group.access);
                           }
                        else {
                           accessSelector.text(client.data.Group.getAccessLevelLabel(group.access));
                           }
                        var accessDiv = $(document.createElement("div")).addClass("clear").appendTo(groupRow);
                        $(document.createElement("span")).text(lang.GROUP_ACCESS + (editable ? "" : ":")).appendTo(accessDiv);
                        accessSelector.appendTo(accessDiv);
                        if(editable &&!createAfterClose) {
                           utility.createMenuItem("dialog-apply.png", "save", lang.SAVE, editDialogHandler).addClass("right").appendTo(accessDiv);
                           }
                        $(document.createElement("div")).addClass("clear").appendTo(groupRow);
                        var topicList;
                        var memberList;
                        if(!createAfterClose) {
                           if(ownMembership != null && ownMembership.enabled == 1) {
                              var topicListHeader = $(document.createElement("h3")).text(lang.TOPICS_SHARED_WITH_GROUP).appendTo(content);
                              var topicListContainer = $(document.createElement("div")).addClass("groupTopicList").hide().appendTo(content);
                              var topicList = new client.widgets.TreeView( {
                                 columns : lang.GROUP_TOPIC_LIST_HEADERS, container : topicListContainer}
                              );
                              var items = [];
                              var topics = me.page.server.getTopics();
                              topics.each(function() {
                                 var rights = this.getRights(); var includeTopic = false; rights.each(function() {
                                    if(this.group === group && this.access > client.data.TopicAccessConstants.noAccess) {
                                       includeTopic = true; return false; }
                                    }
                                 )if(includeTopic) {
                                    var firstCell = utility.createLink().addClass("label").text(this.title).attr("title", this.category); firstCell.bind("click", this, function(event) {
                                       mainView.openTopic(event.data); mainView.hideView(); }
                                    ); var shareInfo = currentUser === this.author ? "(" + lang.YOU + ")" : this.author.name; var accessInfo = client.data.Topic.getAccessLabel(this.access); var item = new client.widgets.TreeViewItem(firstCell, [shareInfo, accessInfo], [], TOPIC_ICON); item._order = this.id; items.push(item); }
                                 }
                              );
                              items.sort(function(a, b) {
                                 return b._order - a._order; }
                              );
                              topicList.load( {
                                 items : items}
                              );
                              }
                           var groupListHeader = $(document.createElement("h3")).text(lang.MEMBER_LIST).appendTo(content);
                           var listContainer = $(document.createElement("div")).addClass("memberList").appendTo(content);
                           memberList = new client.widgets.TreeView( {
                              columns : lang.MEMBER_LIST_HEADERS, container : listContainer}
                           );
                           items = [];
                           group.getMembers().each(function() {
                              var memberInfo = {
                                 membership : this, admin : editable}; var isAdmin = (this.level & client.data.MembershipLevelConstants.admin) == client.data.MembershipLevelConstants.admin; var firstCell, levelCell; var levelLabel; if(this.enabled == 1) {
                                 levelLabel = isAdmin ? lang.ADMIN_LEVEL : lang.MEMBER_LEVEL; }
                              else if(this.enabled == 0) {
                                 levelLabel = lang.REQUESTED; }
                              else if(this.enabled >= 2) {
                                 levelLabel = lang.INVITED; }
                              if(editable && this.enabled == 1) {
                                 levelCell = utility.createLink().addClass("label").text(levelLabel); levelCell.bind("click", memberInfo, memberLevelClickHandler); }
                              else {
                                 levelCell = levelLabel; }
                              if(editable || (this === ownMembership)) {
                                 firstCell = utility.createLink().addClass("label").text(this.user === currentUser ? "(" + lang.YOU + ")" : this.user.name); firstCell.bind("click", memberInfo, memberClickHandler); }
                              else {
                                 firstCell = this.user.name; }
                              var item = new client.widgets.TreeViewItem(firstCell, [levelCell], [], USER_ICON); memberInfo.item = item; items.push(item); }
                           );
                           memberList.load( {
                              items : items}
                           );
                           if(ownMembership != null && ownMembership.enabled == 1) {
                              var handler = function(event) {
                                 if(event.data.hasClass("expanded")) {
                                    event.data.triggerHandler("click");
                                    }
                                 };
                              topicListHeader.bind("click", groupListHeader, handler).makeCollapsible().triggerHandler("click");
                              groupListHeader.bind("click", topicListHeader, handler).makeCollapsible();
                              }
                           }
                        var isMember = ownMembership != null;
                        var isAdmin = isMember && (ownMembership.level & client.data.MembershipLevelConstants.admin) == client.data.MembershipLevelConstants.admin;
                        var toolbar = utility.createToolbar().appendTo(content);
                        if(createAfterClose) {
                           utility.createMenuItem("dialog-apply.png", "ok", lang.CREATE, editDialogHandler).addClass("right").appendTo(toolbar);
                           }
                        var cancelButton = utility.createMenuItem(createAfterClose ? "process-stop.png" : "go-previous.png", null, createAfterClose ? lang.CANCEL : lang.CLOSE, closeGroupEditor).css("float", "right").appendTo(toolbar);
                        if(!createAfterClose) {
                           if(!isMember) {
                              if(group.access & client.data.GroupAccessConstants.allowJoin) {
                                 utility.createMenuItem("group.png", "join", lang.JOIN_GROUP, editDialogHandler).appendTo(toolbar);
                                 }
                              else {
                                 utility.createMenuItem("group.png", "request", lang.REQUEST_GROUP, editDialogHandler).appendTo(toolbar);
                                 }
                              }
                           else {
                              utility.createMenuItem("exit.png", "leave", lang.LEAVE_GROUP, editDialogHandler).appendTo(toolbar);
                              if(isAdmin || (group.access > client.data.GroupAccessConstants.noAccess)) {
                                 utility.createMenuItem("user.png", "invite", lang.INVITE_USER, editDialogHandler).appendTo(toolbar);
                                 }
                              }
                           }
                        groupEditor = $.extend(groupEditor, {
                           nameField : nameField, categoryField : categoryField, accessSelector : accessSelector, group : group, memberList : memberList, create : (createAfterClose !== undefined && createAfterClose)}
                        );
                        }
                     function memberClickHandler(event) {
                        event.stopPropagation();
                        var membership = event.data.membership;
                        var isMe = membership.user == me.page.server.getCurrentUser();
                        if(event.data.admin || isMe) {
                           hideMenu();
                           var parent = groupEditor.memberList.getContainer();
                           menu = utility.createMenu().appendTo(parent);
                           var pos = utility.getRelativePosition(this, parent.get(0));
                           pos.top += this.offsetHeight, menu.css(pos);
                           $.extend(menu, event.data);
                           if(isMe) {
                              if(membership.enabled >= 2) {
                                 menu.append(utility.createMenuItem("group.png", null, lang.ACCEPT_INVITATION, requestActionHandler, {
                                    action : "join", membership : membership}
                                 ));
                                 }
                              var label;
                              if(membership.enabled == 0) {
                                 label = lang.CANCEL_REQUEST;
                                 }
                              else if(membership.enabled >= 2) {
                                 label = lang.REJECT_INVITATION;
                                 }
                              else {
                                 label = lang.LEAVE_GROUP;
                                 }
                              menu.append(utility.createMenuItem("edit-delete.png", null, label, requestActionHandler, {
                                 action : "remove", membership : membership}
                              ));
                              }
                           else {
                              if(membership.enabled == 0) {
                                 if(membership.enabled == 0) {
                                    menu.append(utility.createMenuItem("group.png", null, lang.ACCEPT_MEMBER, requestActionHandler, {
                                       action : "accept", membership : membership}
                                    ));
                                    }
                                 menu.append(utility.createMenuItem("edit-delete.png", null, lang.REJECT_MEMBER, requestActionHandler, {
                                    action : "reject", membership : membership}
                                 ));
                                 }
                              else {
                                 menu.append(utility.createMenuItem("edit-delete.png", "remove", lang.REMOVE_MEMBER, memberMenuHandler));
                                 }
                              }
                           }
                        }
                     function memberMenuHandler(event) {
                        var button = event.data;
                        var item = menu.item;
                        switch(button.action) {
                           case"remove" : if(menu.membership.user == me.page.server.getCurrentUser()) {
                              alert(lang.CANNOT_LEAVE_GROUP_FROM_EDITOR);
                              return;
                              }
                           me.page.server.cancelMembership(menu.membership, utility.createCallback(me, function(data) {
                              if(!data.success) {
                                 alert(lang.COULD_NOT_REMOVE_MEMBER); }
                              }
                           ));
                           break;
                           default : alert("not implemented");
                           break;
                           }
                        hideMenu();
                        }
                     function removeItem(list, item) {
                        var parent = item.row.parent;
                        list.removeItem(item);
                        if(parent != null && parent.children.length == 0) {
                           removeItem(list, parent);
                           }
                        }
                     function memberLevelClickHandler(event) {
                        event.stopPropagation();
                        if(event.data.admin) {
                           hideMenu();
                           var parent = groupEditor.floatObj.content.get(0);
                           menu = utility.createMenu().appendTo(parent);
                           var pos = utility.getRelativePosition(this, parent);
                           pos.top += this.offsetHeight;
                           menu.css(pos);
                           $.extend(menu, event.data);
                           if(event.data.admin) {
                              menu.append(utility.createMenuItem("groups.png", "set" + client.data.MembershipLevelConstants.admin, lang.SET_TO + " " + lang.ADMIN_LEVEL, memberLevelMenuHandler));
                              menu.append(utility.createMenuItem("groups.png", "set" + client.data.MembershipLevelConstants.member, lang.SET_TO + " " + lang.MEMBER_LEVEL, memberLevelMenuHandler));
                              }
                           }
                        }
                     function memberLevelMenuHandler(event) {
                        var button = event.data;
                        var level = 0;
                        var item = menu.item;
                        switch(button.action) {
                           case"set" + client.data.MembershipLevelConstants.admin : level = client.data.MembershipLevelConstants.admin;
                           break;
                           case"set" + client.data.MembershipLevelConstants.member : level = client.data.MembershipLevelConstants.member;
                           break;
                           default : return;
                           break;
                           }
                        if(menu.membership.level != level) {
                           me.page.server.updateMembership(menu.membership, level, menu.membership.enabled, utility.createCallback(me, function(data) {
                              if(data.success) {
                                 var isAdmin = (data.membership.level & client.data.MembershipLevelConstants.admin) == client.data.MembershipLevelConstants.admin; var levelLabel = isAdmin ? lang.ADMIN_LEVEL : lang.MEMBER_LEVEL; $("a", item.row.row).eq(1).text(levelLabel); }
                              else {
                                 alert(lang.COULD_NOT_SET_MEMBER_ACCESS); }
                              }
                           ));
                           }
                        hideMenu();
                        }
                     var menu;
                     function editDialogHandler(event) {
                        var button = event.data;
                        var group = groupEditor.group;
                        switch(button.action) {
                           case"ok" : var name = groupEditor.nameField.text();
                           var category = groupEditor.categoryField.text();
                           var access = parseInt(groupEditor.accessSelector.val());
                           group.name = name;
                           group.category = category;
                           group.access = access;
                           me.page.server.createGroup(group, utility.createCallback(me, updateGroupCallback));
                           groupEditor.share = groupEditor.shareOnCreate;
                           break;
                           case"save" : var name = groupEditor.nameField.text();
                           var category = groupEditor.categoryField.text();
                           var access = parseInt(groupEditor.accessSelector.val());
                           if(name != group.name || category != group.category || access != group.access) {
                              me.page.server.updateGroup(group, {
                                 name : name, category : category, access : access}
                              , utility.createCallback(me, updateGroupCallback));
                              }
                           else {
                              alert(lang.NO_GROUP_CHANGES);
                              }
                           break;
                           case"edit" : case"view" : editGroup(group);
                           break;
                           case"delete" : var members = group.getMembers();
                           if(members.length > 1) {
                              alert(lang.CANNOT_REMOVE_NONEMPTY_GROUP);
                              return;
                              }
                           me.page.server.leaveGroup(group, utility.createCallback(me, function(data) {
                              if(!data.success) {
                                 alert(lang.COULD_NOT_DELETE_GROUP); }
                              else {
                                 updateGroupRightsList(); }
                              }
                           ));
                           break;
                           case"leave" : me.page.server.leaveGroup(group, utility.createCallback(me, function(data) {
                              if(data.success) {
                                 closeGroupEditor(); updateGroupRightsList(); }
                              else {
                                 alert(lang.COULD_NOT_LEAVE_GROUP); }
                              }
                           ));
                           break;
                           case"invite" : hideMenu();
                           event.stopPropagation();
                           showInviteDialog.call(this);
                           return;
                           break;
                           case"join" : case"request" : joinGroup(group);
                           break;
                           }
                        }
                     function joinGroup(group) {
                        me.page.server.joinGroup(group, utility.createCallback(me, function(data) {
                           if(data.success) {
                              updateEditor(data.group); if(data.state == 1) {
                                 refreshGroups(); refreshTopics(); }
                              }
                           else {
                              alert(lang.COULD_NOT_JOIN_GROUP); }
                           }
                        ));
                        }
                     function refreshGroups() {
                        me.page.server.loadGroupList(utility.createCallback(me, loadGroupListCallback));
                        }
                     function refreshTopics() {
                        me.page.server.loadTopicList(utility.createCallback());
                        }
                     function loadGroupListCallback(data) {
                        if(!data.success) {
                           alert(lang.COULD_NOT_LOAD_GROUPS);
                           }
                        }
                     function updateGroupCallback(data) {
                        if(!data.success) {
                           alert(lang.COULD_NOT_UPDATE_GROUP)return;
                           }
                        else {
                           var isCreate = groupEditor.create;
                           if(isCreate) {
                              if(groupList !== undefined) {
                                 groupList.search(data.group.name);
                                 }
                              }
                           updateEditor(data.group);
                           }
                        };
                     function closeGroupEditor(allowSharesManager) {
                        if(groupEditor !== undefined) {
                           var showSharing = allowSharesManager && groupEditor.share;
                           var groupToShareId;
                           if(showSharing) {
                              groupToShareId = groupEditor.group.id;
                              }
                           groupEditor.floatObj.restore();
                           groupEditor = undefined;
                           if(showSharing) {
                              if(showView(client.pageplugins.MainViewToolConstants.sharingManager) && sharingEditor !== undefined) {
                                 sharingEditor.addShareButton.triggerHandler("click");
                                 if(addShareDialog !== undefined) {
                                    addShareDialog.groupSelector.val(groupToShareId.toString());
                                    }
                                 }
                              }
                           }
                        };
                     function updateGroupList() {
                        if(groupList !== undefined) {
                           var term = groupList.getLastTerm();
                           if(term !== undefined) {
                              groupList.load( {
                                 term : term}
                              );
                              }
                           }
                        if(requestList !== undefined) {
                           var requestItems = [];
                           var pendingItems = getPendingItems();
                           var item;
                           pendingItems.each(function() {
                              this.each(function() {
                                 item = createRequestListItem(this, false); requestItems.push(item); }
                              ); }
                           );
                           requestList.unload();
                           requestList.load( {
                              items : requestItems}
                           );
                           }
                        if(groupList !== undefined) {
                           groupList.load();
                           groupList.search();
                           }
                        }
                     function createRequestListItem(membership, onlyReturnActions) {
                        var GROUP_ICON = "group-small.png";
                        var firstCell = $(document.createElement("a")).addClass("label");
                        var actions = [];
                        if(membership.user == me.page.server.getCurrentUser()) {
                           if(membership.enabled >= 2) {
                              actions.push(utility.createLink(lang.ACCEPT_INVITATION, requestActionHandler, {
                                 action : "join", membership : membership}
                              ).addClass("margin").get(0));
                              }
                           actions.push(utility.createLink(membership.enabled < 2 ? lang.CANCEL_REQUEST : lang.REJECT_INVITATION, requestActionHandler, {
                              action : "remove", membership : membership}
                           ).addClass("margin").get(0));
                           }
                        else {
                           actions.push(utility.createLink(lang.ACCEPT_MEMBER, requestActionHandler, {
                              action : "accept", membership : membership}
                           ).addClass("margin").get(0));
                           actions.push(utility.createLink(lang.REJECT_MEMBER, requestActionHandler, {
                              action : "reject", membership : membership}
                           ).addClass("margin").get(0));
                           }
                        var infoLabel;
                        if(membership.enabled >= 2) {
                           infoLabel = lang.INVITATION;
                           }
                        else {
                           var currentUser = me.page.server.getCurrentUser();
                           infoLabel = lang.REQUEST_BY + " ";
                           if(membership.user === currentUser) {
                              infoLabel += lang.BY_YOU;
                              }
                           else {
                              infoLabel += membership.user.name;
                              }
                           }
                        requestItem = new client.widgets.TreeViewItem(createGroupLink(membership.group), [infoLabel, $(actions)], [], GROUP_ICON);
                        if(!onlyReturnActions) {
                           return requestItem;
                           }
                        else {
                           return actions;
                           }
                        }
                     function requestActionHandler(event) {
                        var action = event.data.action;
                        var membership = event.data.membership;
                        switch(action) {
                           case"remove" : me.page.server.leaveGroup(membership.group, utility.createCallback(me, function(data) {
                              if(!data.success) {
                                 alert(lang.COULD_NOT_REMOVE_REQUEST); }
                              else {
                                 updateGroupRightsList(); }
                              }
                           ));
                           break;
                           case"accept" : case"reject" : var reject = (action == "reject");
                           me.page.server.acceptMembership(membership, reject ? true : false, utility.createCallback(me, function(data) {
                              if(!data.success) {
                                 alert(reject ? lang.COULD_NOT_REJECT_REQUEST : lang.COULD_NOT_ACCEPT_REQUEST); }
                              }
                           ));
                           break;
                           case"join" : joinGroup(membership.group);
                           break;
                           default : alert("not implemented");
                           break;
                           }
                        }
                     function createGroupLink(group) {
                        return utility.createLink().bind("click", group, function(event) {
                           editGroup(event.data); }
                        ).text(group.name).attr("title", group.description);
                        }
                     var inviteesList;
                     function showInviteDialog() {
                        var group = groupEditor.group;
                        var content = $(document.createElement("div"));
                        var users = me.page.server.getUsers();
                        var currentUser = me.page.server.getCurrentUser();
                        var externalUsers = me.page.server.getExternalUsers();
                        var invitees = [];
                        users.each(function() {
                           if(this.getMembershipInGroup(group) == null && this !== currentUser && this.id !== null && this.name !== null) {
                              var email = "(" + lang.UNKNOWN + ")"; if(this.email !== undefined && this.email != null) {
                                 email = this.email; }
                              invitees.push(createInvitee(this, email)); }
                           }
                        );
                        if(externalUsers !== undefined) {
                           externalUsers.each(function() {
                              if(this.mb_id === undefined || group.getMemberByUserId(this.mb_id) == null) {
                                 var email = "(" + lang.UNKNOWN + ")"; if(this.email !== undefined && this.email != null && this.email.toString().length > 0) {
                                    email = this.email; }
                                 invitees.push(createInvitee(this, email)); }
                              }
                           );
                           }
                        invitees.sort(function(a, b) {
                           var sortA = a.title; var sortB = b.title; if(sortA == sortB) {
                              return 0; }
                           return(sortA.toUpperCase() > sortB.toUpperCase()) ? 1 :- 1; }
                        );
                        if(invitees.length > 0) {
                           content.append($(document.createElement("h3")).text(lang.KNOWN_USERS));
                           var listContainer = $(document.createElement("div")).appendTo(content).css( {
                              "max-height" : 100, "overflow-y" : "auto"}
                           );
                           inviteesList = new client.widgets.TreeView( {
                              columns : lang.INVITE_LIST_HEADERS, container : listContainer, multiSelect : true, showCheckBoxes : true}
                           );
                           inviteesList.load( {
                              items : invitees}
                           );
                           }
                        content.append($(document.createElement("h3")).css("clear", "both").text(lang.FURTHER_USERS_INVITE));
                        var emailRow = utility.createToolbar().appendTo(content);
                        $(document.createElement("span")).text(lang.EMAIL_OR_NAME).appendTo(emailRow);
                        var emailField = $(document.createElement("textarea")).attr( {
                           "cols" : "40", "rows" : "3"}
                        ).appendTo(emailRow);
                        $(document.createElement("p")).text(lang.EMAIL_SEPARATE_EXPLANATION).appendTo(emailRow);
                        var parent = groupEditor.floatObj.content;
                        var pos = {
                           left : 60, top : 40, width : "80%"};
                        var panel = utility.createPanelDialog(content, pos, inviteDialogHandler, lang.INVITE_USERS_TO_GROUP, lang.INVITE);
                        panel.appendTo(parent);
                        menu = panel;
                        menu.emailField = emailField;
                        menu.checkBoxes = invitees;
                        menu.group = group;
                        function createInvitee(user, email) {
                           var invitee = new client.widgets.TreeViewItem(user.name, [email], [], "user-small.png", false);
                           invitee.user = user;
                           return invitee;
                           }
                        }
                     function inviteDialogHandler(event) {
                        switch(event.data.action) {
                           case"ok" : var ids = [];
                           var externalIds = [];
                           menu.checkBoxes.each(function() {
                              if(this.isChecked()) {
                                 if(this.user instanceof client.data.User) {
                                    ids.push(this.user.id); }
                                 else {
                                    externalIds.push(this.user.id); }
                                 }
                              }
                           );
                           var addressesRaw = menu.emailField.val().replace(/\n/g,",").split(",");
                           var addresses = [];
                           addressesRaw.each(function() {
                              var val = this.trim(); if(val.length > 0) {
                                 addresses.push(val); }
                              }
                           );
                           if(addresses.length > 0 || ids.length > 0 || externalIds.length > 0) {
                              me.page.server.inviteToGroup(menu.group, ids, externalIds, addresses, utility.createCallback(me, function(data) {
                                 if(data.success) {
                                    hideMenu(); if(data.notfound !== undefined) {
                                       var notFound = data.notfound; alert(lang.FOLLOWING_NOT_FOUND + notFound.join(", ") + "."); }
                                    else {
                                       if(!data.invited) {
                                          alert(lang.NONE_INVITED); }
                                       }
                                    if(data.invited !== undefined) {
                                       refreshGroups(); }
                                    }
                                 else {
                                    alert(lang.COULD_NOT_INVITE_USERS); }
                                 }
                              ));
                              }
                           else {
                              alert(lang.NO_INVITEES_SELECTED);
                              }
                           break;
                           case"cancel" : hideMenu();
                           break;
                           }
                        }
                     function hideMenu() {
                        if(menu) {
                           menu.remove();
                           menu = null;
                           }
                        }
                     function setupSharingEditor(topic) {
                        var panel = $(document.createElement("div"));
                        $(document.createElement("h3")).text(lang.GROUPLIST_HEADER).appendTo(panel);
                        var container = $(document.createElement("div")).css( {
                           "overflow-x" : "auto", "max-height" : 160}
                        ).appendTo(panel);
                        var treeView = new client.widgets.TreeView( {
                           columns : lang.GROUPLIST_HEADINGS, container : container}
                        );
                        var addShareButton;
                        if((topic.access & client.data.TopicAccessConstants.owner) == client.data.TopicAccessConstants.owner) {
                           var sharingToolbar = utility.createToolbar().appendTo(panel);
                           addShareButton = utility.createMenuItem("map-share.png", "addshare", lang.ADD_GROUP, sharingToolbarHandler).appendTo(sharingToolbar);
                           utility.createMenuItem("group-find.png", null, lang.FIND_GROUP, showView, client.pageplugins.MainViewToolConstants.studentFinder).addClass("right").appendTo(sharingToolbar);
                           utility.createMenuItem("group-create.png", "sharing", lang.CREATE_GROUP, createGroupHandler).addClass("right").appendTo(sharingToolbar);
                           }
                        return {
                           topic : topic, treeView : treeView, container : panel, addShareButton : addShareButton};
                        }
                     function createGroupHandler(event) {
                        var shareAfterCreate = false;
                        var topic = mainView.getTopic();
                        if(topic !== undefined && lastView == client.pageplugins.MainViewToolConstants.sharingManager) {
                           shareAfterCreate = true;
                           }
                        var group = new client.data.Group();
                        group.name = lang.UNTITLED;
                        group.access = client.data.GroupAccessConstants.allowView;
                        editGroup(group, true, shareAfterCreate);
                        }
                     var addShareDialog;
                     function sharingToolbarHandler(event) {
                        var button = event.data;
                        switch(button.action) {
                           case"addshare" : if(addShareDialog !== undefined) {
                              return;
                              }
                           var currentUser = me.page.server.getCurrentUser();
                           var groups = me.page.server.getGroups();
                           sharingEditor.topic.getRights().each(function() {
                              groups.remove(this.group); }
                           );
                           var hasGroup = false;
                           groups.each(function() {
                              var membership = currentUser.getMembershipInGroup(this); if(membership != null && membership.enabled == 1) {
                                 hasGroup = true; return false; }
                              return true; }
                           );
                           if(!hasGroup) {
                              if(sharingEditor.topic.getRightsCount() > 0) {
                                 alert(lang.ALREADY_SHARED_WITH_ALL_GROUPS);
                                 }
                              else {
                                 alert(lang.NOT_MEMBER_OF_ANY_GROUP);
                                 }
                              return;
                              }
                           showAddShareDialog(groups, this);
                           break;
                           }
                        }
                     function showAddShareDialog(groups, link) {
                        var content = $(document.createElement("div"));
                        var currentUser = me.page.server.getCurrentUser();
                        var groupSelector = $(document.createElement("select")).addClass("large").css("max-width", 150);
                        pos = {
                           top : link.offsetHeight + 6, left : 0, width : DIALOG_WIDTH, "max-height" : DIALOG_HEIGHT};
                        if(sharingEditor.topic.access == client.data.TopicAccessConstants.owner) {
                           var titleBar = utility.createToolbar().appendTo(content);
                           titleBar.append($(document.createElement("span")).text(lang.FILE_AS));
                           var titleBox = $.createEditableLabel(null, utility.createCallback(this, function(cancelArgs) {
                              sharingEditor.newTopicTitle = cancelArgs.text; if(cancelArgs.text == sharingEditor.topic.title) {
                                 delete sharingEditor.newTopicTitle; }
                              }
                           ), document, 60).text(sharingEditor.topic.title).appendTo(titleBar);
                           pos["max-height"] += 30;
                           }
                        var groupBar = utility.createToolbar().appendTo(content);
                        groupBar.append($(document.createElement("span")).text(lang.GROUP_NAME));
                        var groupsSorted = groups.clone();
                        groupsSorted.sort(function(a, b) {
                           var sortA = a.name; var sortB = b.name; if(sortA == sortB) {
                              return 0; }
                           return(sortA.toUpperCase() > sortB.toUpperCase()) ? 1 :- 1; }
                        );
                        groupsSorted.each(function() {
                           var membership = currentUser.getMembershipInGroup(this); if(membership != null && membership.enabled == 1) {
                              var option; groupSelector.append(option = $(document.createElement("option")).text(this.name).val(this.id)); }
                           }
                        );
                        groupSelector.appendTo(groupBar);
                        var rightsBar = utility.createToolbar().appendTo(content);
                        rightsBar.append($(document.createElement("span")).text(lang.MEMBER_RIGHTS));
                        var rightSelector = $(document.createElement("select")).addClass("large").css("max-width", 150);
                        rightSelector.append($(document.createElement("option")).text(lang.RIGHT_READONLY_PRESET).val(client.data.TopicAccessConstants.presetReadOnly));
                        rightSelector.append($(document.createElement("option")).text(lang.RIGHT_WRITE_PRESET).val(client.data.TopicAccessConstants.presetWriteAccess));
                        rightSelector.append($(document.createElement("option")).text(lang.RIGHT_STUCTURE_PRESET).val(client.data.TopicAccessConstants.presetFullAccess));
                        rightSelector.val(client.data.TopicAccessConstants.presetFullAccess.toString());
                        rightSelector.appendTo(rightsBar);
                        addShareDialog = utility.createPanelDialog(content, pos, addShareDialogHandler, lang.SHARE_TOPIC_WITH_GROUP, lang.CREATE);
                        addShareDialog.groupSelector = groupSelector;
                        addShareDialog.rightSelector = rightSelector;
                        addShareDialog.appendTo($(link).parent().css("position", "relative"));
                        }
                     function addShareDialogHandler(event) {
                        var button = event.data;
                        switch(button.action) {
                           case"ok" : var right = addShareDialog.right;
                           var groupId = addShareDialog.groupSelector.val();
                           var access = addShareDialog.rightSelector.val();
                           var group = null;
                           me.page.server.getGroups().each(function() {
                              if(this.id == groupId) {
                                 group = this; return false; }
                              return true; }
                           );
                           if(group != null) {
                              if(sharingEditor.newTopicTitle !== undefined) {
                                 me.page.server.updateTopic(sharingEditor.topic, {
                                    title : sharingEditor.newTopicTitle}
                                 , utility.createCallback());
                                 }
                              me.page.server.shareTopic(sharingEditor.topic, group, access, utility.createCallback(me, function(data) {
                                 if(data.success) {
                                    hideAddShareDialog(); }
                                 else {
                                    alert(lang.COULD_NOT_UPDATE_RIGHT); }
                                 }
                              ));
                              }
                           break;
                           case"cancel" : hideAddShareDialog();
                           break;
                           }
                        }
                     function updateGroupRightsList() {
                        if(!sharingEditor) {
                           return;
                           }
                        var items = [];
                        var topic = sharingEditor.topic;
                        var isOwner = (topic.access == client.data.TopicAccessConstants.owner);
                        var treeView = sharingEditor.treeView;
                        topic.getRights().each(function() {
                           var GROUP_ICON = "group-small.png"; var accessRights = client.data.Topic.getAccessLabel(this.access); var accessRightsButton; if(isOwner) {
                              accessRightsButton = utility.createLink().attr( {
                                 title : lang.CLICK_TO_CHANGE_ACCESS}
                              ).text(accessRights).bind("click", this, sharingAccessHandler); }
                           else {
                              accessRightsButton = accessRights; }
                           var editLink = createGroupLink(this.group); items.push(new client.widgets.TreeViewItem(editLink, [accessRightsButton], null, "group-small.png"))}
                        );
                        if(topic.isShared()) {
                           licenseEditor.container.show();
                           }
                        else {
                           licenseEditor.container.hide();
                           }
                        treeView.unload();
                        treeView.load( {
                           items : items}
                        );
                        }
                     function sharingAccessHandler(event) {
                        var right = event.data;
                        if(right.topic.access != client.data.TopicAccessConstants.owner) {
                           return;
                           }
                        if(menu) {
                           menu.remove();
                           }
                        event.stopPropagation();
                        var button = event.target;
                        menu = utility.createMenu();
                        utility.createMenuItem("private.png", null, lang.SET_ACCESS_NONE, sharingAccessMenuItemHandler, {
                           action : "accessNone", right : right}
                        ).appendTo(menu);
                        utility.createMenuItem("public-read.png", null, lang.SET_ACCESS_READ, sharingAccessMenuItemHandler, {
                           action : "accessRead", right : right}
                        ).appendTo(menu);
                        utility.createMenuItem("public-write.png", null, lang.SET_ACCESS_WRITE, sharingAccessMenuItemHandler, {
                           action : "accessWrite", right : right}
                        ).appendTo(menu);
                        utility.createMenuItem("public-write.png", null, lang.SET_ACCESS_STRUCTURE, sharingAccessMenuItemHandler, {
                           action : "accessStructure", right : right}
                        ).appendTo(menu);
                        menu.appendTo(event.target.parentNode);
                        menu.focusFirstEditable();
                        }
                     function sharingAccessMenuItemHandler(event) {
                        var info = event.data;
                        var right = info.right;
                        var newAccessLevel = right.access;
                        switch(info.action) {
                           case"accessNone" : newAccessLevel = client.data.TopicAccessConstants.noAccess;
                           break;
                           case"accessRead" : newAccessLevel = client.data.TopicAccessConstants.presetReadOnly;
                           break;
                           case"accessWrite" : newAccessLevel = client.data.TopicAccessConstants.presetWriteAccess;
                           break;
                           case"accessStructure" : newAccessLevel = client.data.TopicAccessConstants.presetFullAccess;
                           break;
                           }
                        me.page.server.updateRight(right, newAccessLevel, utility.createCallback(me, function(data) {
                           if(!data.success) {
                              alert(lang.ERROR_CHANGING_STATUS); }
                           }
                        ));
                        menu.remove();
                        menu = null;
                        }
                     function setupLicenseEditor(topic, isEditor) {
                        var container = $(document.createElement("div"));
                        $(document.createElement("h3")).text(lang.LICENSE_HEADER).appendTo(container);
                        var toolbar = utility.createToolbar().appendTo(container);
                        var licensable = isEditor && (topic.access & client.data.TopicAccessConstants.owner) == client.data.TopicAccessConstants.owner;
                        toolbar.append($(document.createElement("span")).text(lang.LICENSE_TEXT + (licensable ? "" : (": " + getLicenseTitle(topic.license)))));
                        if(licensable) {
                           var licenseSelector = $(document.createElement("select")).appendTo(toolbar).addClass("large");
                           for(var i = 0; i < 7; i++) {
                              licenseSelector.append($(document.createElement("option")).val(i).text(getLicenseTitle(i)));
                              }
                           licenseSelector.css("max-width", 200);
                           licenseSelector.val(topic.license);
                           licenseSelector.bind("change", topic, function(event) {
                              event.stopPropagation(); var relatedTopic = event.data; var license = parseInt($(this).val()); me.page.server.updateTopicLicense(relatedTopic, license, utility.createCallback(me, function(data) {
                                 if(data.success) {
                                    }
                                 else {
                                    alert(lang.COULD_NOT_CHANGE_TOPIC_LICENSE); }
                                 }
                              ))}
                           )}
                        toolbar.append($(document.createElement("a")).text(lang.LICENSE_EXPLANATION).addClass("label").attr( {
                           "href" : lang.LICENSE_EXPLANATION_LINK, "target" : "_blank"}
                        ));
                        if(licensable) {
                           toolbar.after(utility.createToolbar().append($(document.createElement("span")).text(lang.LICENSE_APPLICATION)));
                           }
                        return {
                           container : container};
                        }
                     function getLicenseTitle(license) {
                        switch(license) {
                           case 0 : return lang.LICENSE_NO_RESERVED;
                           case 1 : return lang.LICENSE_BY;
                           case 2 : return lang.LICENSE_BY_SA;
                           case 3 : return lang.LICENSE_BY_ND;
                           case 4 : return lang.LICENSE_BY_NC;
                           case 5 : return lang.LICENSE_BY_NC_SA;
                           case 6 : return lang.LICENSE_BY_NC_ND;
                           default : return lang.LICENSE_NO_RESERVED;
                           }
                        }
                     function hideAddShareDialog() {
                        if(addShareDialog !== undefined) {
                           addShareDialog.remove();
                           addShareDialog = undefined;
                           }
                        }
                     function closeHandler() {
                        closeGroupEditor();
                        hideAddShareDialog();
                        if(groupList !== undefined) {
                           groupList.destroy();
                           groupList = undefined;
                           }
                        if(requestList !== undefined) {
                           requestList.destroy();
                           requestList = undefined;
                           }
                        if(problemList !== undefined) {
                           problemList.destroy();
                           problemList = undefined;
                           }
                        if(inviteesList !== undefined) {
                           inviteesList.destroy();
                           inviteesList = undefined;
                           }
                        }
                     }
                  client.pageplugins.Community.prototype = new client.PagePlugin;
                  client.pageplugins.Home = function() {
                     var NOTIFICATION_CHECK_PAUSE = 10000;
                     var me;
                     this.load = function() {
                        me = this;
                        me.page.server.registerSyncHandler("notifications", notificationsChangedHandler);
                        }
                     function notificationsChangedHandler() {
                        updateNotificationList();
                        }
                     this.getPreview = function() {
                        var panel = $(document.createElement("div"));
                        loader = $(document.createElement("div")).addClass("loader").appendTo(panel);
                        $(document.createElement("h3")).text(lang.NEWS).appendTo(panel);
                        loadNotifications();
                        notificationList = $(document.createElement("div")).addClass("messages").appendTo(panel);
                        updateNotificationList();
                        return panel;
                        }
                     var lastNotificationRefresh;
                     function loadNotifications(force) {
                        var end = new Date();
                        if(force || lastNotificationRefresh === undefined || (end.getTime() - lastNotificationRefresh.getTime() > NOTIFICATION_CHECK_PAUSE)) {
                           loader.addClass("show");
                           me.page.server.loadNotifications(utility.createCallback(me, loadListCallback));
                           lastNotificationRefresh = end;
                           }
                        }
                     function loadListCallback(data) {
                        loader.removeClass("show");
                        if(data.error !== undefined) {
                           alert(lang.COULD_NOT_RETRIEVE_NOTIFICATION_MESSAGE);
                           }
                        }
                     var notificationList;
                     function updateNotificationList() {
                        if(notificationList === undefined) {
                           return;
                           }
                        var messages = me.page.server.getNotifications();
                        $("div.message", notificationList).remove();
                        if(messages.length > 0) {
                           messages.each(function() {
                              var notification = $(document.createElement("div")).addClass("message").appendTo(notificationList); var remover = utility.createButton("edit-delete-small.png", null, lang.REMOVE).addClass("closer right").appendTo(notification); remover.appendTo(notification); remover.one("click", this, function(event) {
                                 me.page.server.markNotificationAsRead(event.data, utility.createCallback(this, markNotificationAsReadCallback)); }
                              ); var message; if(lang.NOTIFICATION_MESSAGES[this.messageType] !== undefined) {
                                 message = lang.NOTIFICATION_MESSAGES[this.messageType]; }
                              utility.createLink().addClass("title").text(lang.NOTIFICATION_TITLES[this.messageType] !== undefined ? lang.NOTIFICATION_TITLES[this.messageType] : this.feedTitle).appendTo(notification).bind("click", this, notificationClickHandler); if(message !== undefined) {
                                 $(document.createElement("div")).addClass("info").text(message).appendTo(notification); }
                              }
                           );
                           }
                        else {
                           notificationList.append($(document.createElement("div")).addClass("message").html(lang.NO_NOTIFICATIONS_HTML));
                           }
                        }
                     function markNotificationAsReadCallback(data) {
                        if(data.success) {
                           $(this).parent().remove();
                           }
                        else {
                           alert(lang.COULD_NOT_HIDE_MESSAGE);
                           }
                        }
                     function notificationClickHandler(event) {
                        var notification = event.data;
                        }
                     }
                  client.pageplugins.Home.prototype = new client.PagePlugin;
                  client.pageplugins.Organization = function() {
                     client.PagePlugin.call(this);
                     }
                  client.pageplugins.Organization.prototype = new client.PagePlugin;
                  client.pageplugins.NoteDisplay = function(args) {
                     client.PagePlugin.call(this);
                     var SAVE_TIMEOUT = 7;
                     var SAVE_RETIMEOUT = 3;
                     var PERMANENT_HIGHLIGHTING = true;
                     var balloonConfig, currentBalloon;
                     var me, registeredPlugins, registeredMarkers;
                     this.load = function() {
                        me = this;
                        registeredPlugins = me.page.server.getDisplayPlugins();
                        registeredMarkers = me.page.server.getMarkerPlugins();
                        displayInterface = new client.pageplugins.NoteDisplayInterface();
                        setupUI();
                        var currentUser = this.page.server.getCurrentUser();
                        var settings = currentUser.settings;
                        if(settings.displayBalloons === undefined) {
                           settings.displayBalloons = {
                              };
                           }
                        balloonConfig = settings.displayBalloons;
                        client.pageplugins.NoteDisplay.markerClipboard = [];
                        client.PagePlugin.prototype.load.call(this);
                        }
                     var mainView;
                     this.onPluginLoad = function(plugin) {
                        if(plugin instanceof client.pageplugins.MainView) {
                           mainView = plugin;
                           }
                        }
                     var isEditor;
                     this.isEditor = function() {
                        return isEditor;
                        }
                     var focusAfterLoad;
                     var loaded;
                     this.openCard = function(card, editable) {
                        this.closeCard();
                        editable = editable && card.step.topic.isEditable();
                        if(card !== currentCard) {
                           focusAfterLoad = true;
                           }
                        currentCard = card;
                        if(core.document == null || (isEditor != editable)) {
                           changeMode.call(this, editable);
                           }
                        else {
                           loadCard.call(this);
                           }
                        }
                     this.closeCard = function() {
                        if(loaded) {
                           unloadCard.call(this);
                           }
                        }
                     this.saveCard = function() {
                        if(loaded) {
                           hideTools();
                           saveCard.call(this);
                           }
                        }
                     var core;
                     var allElements, categoryLabel, toolbar, insertButton, highlightButton, cardContainer, titleBox, titleLabel, coreWrapper, markerBar;
                     var toolbarFadeOutTimer;
                     function setupUI() {
                        titleBox = $(document.createElement("div")).addClass("titleBox").appendTo(args.header);
                        toolbar = utility.createToolbar().hide().css( {
                           "right" : "0", "left" : "auto"}
                        ).appendTo(args.header);
                        var obj = {
                           action : "highlight_menu"};
                        highlightButton = args.pen.bind("click", obj, menuHandler).attr("accesskey", "h").bind("contextmenu", obj, menuHandler);
                        clipboardButton = args.clipboardBox.bind("click", {
                           action : "clipboard_menu"}
                        , menuHandler);
                        clipboardButton = args.clipboardBox.bind("contextmenu", {
                           action : "clipboard_manage"}
                        , menuHandler);
                        cardContainer = $(document.createElement("div")).appendTo(args.container);
                        categoryLabel = utility.createLink().addClass("category").appendTo(titleBox);
                        categoryLabel.click(categoryHandler);
                        mainContainer = $(document.createElement("div")).addClass("mainContainer").appendTo(cardContainer);
                        markerBar = $(document.createElement("div")).addClass("markerBar").appendTo(mainContainer);
                        coreWrapper = $(document.createElement("div")).addClass("coreWrapper").appendTo(mainContainer);
                        coreWrapper.add(toolbar).hover(function() {
                           window.clearTimeout(toolbarFadeOutTimer); toolbar.fadeIn(); toolbar.stop(false, true); }
                        , function() {
                           toolbarFadeOutTimer = window.setTimeout(function() {
                              toolbar.fadeOut(); }
                           , 500); }
                        );
                        core = new client.widgets.Editor( {
                           node : coreWrapper, keyHook : keyHook}
                        );
                        core.setupToolbar( {
                           node : toolbar, noUndo : true, additionalHandlers : {
                              8 : clearFormattingHook}
                           }
                        );
                        utility.createMenuButton(lang.FORMAT, "formatpar", "", menuHandler).appendTo(toolbar);
                        utility.createButtonSeparator().appendTo(toolbar);
                        /*insertButton=utility.createMenuItem("insert-image.png","insert_menu",lang.INSERT,menuHandler).attr("accesskey","i").appendTo(toolbar);insertButton.attr("title",lang.INSERT_EXPLANATION);*/
                        allElements = categoryLabel.add(toolbar).add(cardContainer);
                        }
                     function categoryHandler(event) {
                        mainView.showMap();
                        }
                     function clearFormattingHook(event) {
                        markers.each(function() {
                           this.adjustClass(); updateMarkers(); if(selectedMarker) {
                              selectedMarker.range.addClass("highlight"); }
                           }
                        );
                        }
                     var keyHooks;
                     function keyHook(event) {
                        for(var i = 0; i < keyHooks.length; i++) {
                           if(keyHooks[i].call(this, event) === false) {
                              return false;
                              }
                           }
                        if(event.which == 83 &&!event.shiftKey &&!event.altKey && event.metaKey) {
                           event.preventDefault();
                           event.stopPropagation();
                           event.stopImmediatePropagation();
                           mainView.saveCard();
                           return;
                           }
                        if(saveTimeout !== undefined) {
                           triggerAutoSave();
                           }
                        }
                     var displayInterface;
                     var currentCard;
                     this.getCurrentCard = function() {
                        return currentCard;
                        }
                     var editingTitle;
                     function changeMode(newMode) {
                        if(core.frame != null && isEditor) {
                           if($.browser.mozilla) {
                              utility.mozilla.removeEnterKeyHandler(core.document);
                              }
                           setConfirmUnload(false);
                           }
                        isEditor = newMode;
                        if(titleLabel) {
                           titleLabel.remove();
                           titleLabel = null;
                           }
                        if(isEditor) {
                           titleLabel = $.createEditableLabel(utility.createCallback(this, function(cancelArgs) {
                              categoryLabel.hide(); toolbar.css("visibility", "hidden"); editingTitle = true; }
                           ), utility.createCallback(this, function(cancelArgs) {
                              categoryLabel.show(); toolbar.css("visibility", ""); editingTitle = false; if(currentCard) {
                                 if(cancelArgs.oldText != cancelArgs.text) {
                                    data.title = cancelArgs.text; me.updateTitle(); triggerChange(); }
                                 core.window.focus(); if(noContentNode) {
                                    noContentNode.selectNodes(); }
                                 return false; }
                              }
                           ), document, 60);
                           titleLabel.attr("accesskey", "t");
                           }
                        else {
                           titleLabel = $(document.createElement("span"));
                           }
                        titleLabel.addClass("title").appendTo(titleBox);
                        if(isEditor) {
                           toolbar.children().show();
                           }
                        else {
                           toolbar.children().not(".always").hide();
                           }
                        if(core.document == null) {
                           core.load( {
                              callback : utility.createCallback(this, afterLoad), isEditor : isEditor, attributes : {
                                 scrolling : "no"}
                              , css : {
                                 width : 488}
                              }
                           );
                           core.frame.addClass("editorbox");
                           }
                        else {
                           if(core.changeMode(isEditor)) {
                              afterLoad.call(this);
                              }
                           }
                        }
                     this.updateTitle = function() {
                        if(currentCard && loaded) {
                           categoryLabel.text(currentCard.step.topic.title).attr("title", lang.CATEGORY + ": " + currentCard.step.topic.category);
                           titleLabel.text(data.title || currentCard.title);
                           var catWidth = titleBox.width() - titleLabel.get(0).offsetWidth - 16;
                           if(catWidth > 80) {
                              categoryLabel.show().css("max-width", catWidth);
                              }
                           else {
                              categoryLabel.hide();
                              }
                           }
                        }
                     var noContentNode;
                     var markers;
                     function afterLoad() {
                        displayInterface.body = core.body;
                        displayInterface.document = core.document;
                        displayInterface.window = core.window;
                        displayInterface.frame = core.frame;
                        displayInterface.head = core.head;
                        displayInterface.server = me.page.server;
                        displayInterface.triggerChange = function() {
                           updateBoxes();
                           triggerChange();
                           };
                        displayInterface.displayTool = displayTool;
                        displayInterface.getTopics = getTopics;
                        displayInterface.getCard = getCard;
                        displayInterface.openCard = openCardFromPlugin;
                        displayInterface.addMarker = addMarker;
                        displayInterface.selectMarker = selectMarker;
                        displayInterface.getMarkers = getMarkers;
                        displayInterface.removeMarker = removeMarker;
                        displayInterface.getMarkerBar = getMarkerBar;
                        displayInterface.getMarkerBarOffset = getMarkerBarOffset;
                        displayInterface.getCurrentUser = getCurrentUser;
                        keyHooks = [];
                        displayInterface.addKeyHook = function(hook) {
                           keyHooks.push(hook);
                           };
                        displayInterface.removeKeyHook = function(hook) {
                           keyHooks.remove(hook);
                           };
                        registeredMarkers.each(function() {
                           this.displayInterface = displayInterface; }
                        );
                        if(!isEditor) {
                           core.body.addClass("study-tool");
                           }
                        else {
                           core.body.removeClass("study-tool");
                           if($.browser.mozilla) {
                              utility.mozilla.setupEnterKeyHandler(core.document, replaceNodes);
                              }
                           setConfirmUnload(true);
                           }
                        loadCard.call(this);
                        }
                     function getCurrentUser() {
                        return me.page.sever.getCurrentUser();
                        }
                     var initiallyEmpty;
                     var data;
                     function loadCard() {
                        data = {
                           };
                        sizeWarningShown = false;
                        suppressMarkerUpdate = true;
                        initiallyEmpty = false;
                        noContentNode = null;
                        if(!currentCard.content) {
                           if(isEditor) {
                              core.body.contents().remove();
                              var level = currentCard.step.getLevelOfCard(currentCard);
                              var dummyText;
                              if(level == client.data.LevelConstants.main) {
                                 dummyText = lang.CLICK_TO_ENTER;
                                 }
                              else if(level == client.data.LevelConstants.advanced) {
                                 dummyText = lang.CLICK_TO_ENTER_ADVANCED;
                                 }
                              else if(level == client.data.LevelConstants.illustrative) {
                                 dummyText = lang.CLICK_TO_ENTER_ILLUSTRATIVE;
                                 }
                              noContentNode = $(core.document.createElement("p")).appendTo(core.body);
                              noContentNode.text(dummyText);
                              initiallyEmpty = true;
                              $(core.document).one("click", dummyNodeClickHandler);
                              $($.browser.msie ? core.body : core.document).one("dragenter", dummyNodeClickHandler);
                              }
                           else {
                              core.body.html("<p>" + lang.CARD_EMPTY + "</p>");
                              }
                           }
                        else {
                           core.body.html(currentCard.content);
                           }
                        displayInterface.isEditor = isEditor;
                        if(isEditor) {
                           initialIdentifiedNodes = $("[id]", core.body);
                           currentCard.edited = true;
                           }
                        var removedMarker = false;
                        markers = [];
                        var cardMarkers = currentCard.getMarkers();
                        for(var i = 0; i < cardMarkers.length; i++) {
                           var marker = cardMarkers[i];
                           marker.range = $.fromRangeString(marker.rangeStore, core.body.get(0));
                           if(marker.range != null && marker.range.containsVisibleElements()) {
                              marker.range = $("*", marker.range.parent()).filter(function() {
                                 return marker.range.index(this) !=- 1; }
                              );
                              client.pageplugins.NoteDisplay.markerClipboard.remove(marker);
                              addMarker(marker);
                              }
                           else {
                              removedMarker = true;
                              copyToClipboard(marker);
                              }
                           }
                        markerInserted = false;
                        for(var i = 0; i < registeredPlugins.length; i++) {
                           var registeredPlugin = registeredPlugins[i];
                           registeredPlugin.displayInterface = displayInterface;
                           registeredPlugin.load();
                           }
                        suppressMarkerUpdate = false;
                        if(markers.length > 0) {
                           var images = $("img", core.body);
                           imagesCount = images.length;
                           images.one("load", imageLoadedHandler);
                           }
                        updateBoxes();
                        hasChanged = false || removedMarker;
                        core.body.bind("change", changeHandler);
                        $(core.body).bind("paste", pasteHandler);
                        if($.browser.msie) {
                           $(core.body).bind("drop", dropHandler);
                           }
                        else {
                           $(core.document).bind("dragdrop", dropHandler);
                           }
                        $(core.document).bind("mouseup", editorMouseUpHandler);
                        $(core.document).bind("click", mainClickHandler);
                        $(document).bind("click", outerMainClickHandler);
                        timesChanged = 0;
                        loaded = true;
                        this.updateTitle();
                        if(initiallyEmpty && isEditor) {
                           titleLabel.triggerHandler("click");
                           focusAfterLoad = false;
                           }
                        checkDisplayHeight();
                        if(currentCard.content) {
                           updateMarkers();
                           if(isEditor) {
                              if(lastSelection === undefined || lastSelection.card !== currentCard ||!jQuery.restoreSelection(lastSelection, {
                                 context : core.document}
                              )) {
                                 var a = core.body, b;
                                 while((a = a.contents(":last")).length > 0 && a[0].nodeType == 1) {
                                    b = a;
                                    }
                                 if(b !== undefined) {
                                    var last = b.contents(":last");
                                    b.selectNodes( {
                                       collapse : b.is("br")}
                                    );
                                    }
                                 }
                              lastSelection = undefined;
                              }
                           }
                        if(focusAfterLoad) {
                           focusAfterLoad = false;
                           core.window.focus();
                           }
                        }
                     function editorMouseUpHandler(event) {
                        if(delayHighlight !== undefined) {
                           if($.hasSelection(core.document)) {
                              event.stopPropagation();
                              core.body.removeClass("highlight-cursor");
                              delayHighlight.triggerHandler("click");
                              }
                           else {
                              delayHighlight.removeClass("active");
                              resetDelayHighlight();
                              }
                           }
                        }
                     function resetDelayHighlight() {
                        if(delayHighlight !== undefined) {
                           core.body.removeClass("highlight-cursor");
                           delayHighlight.removeClass("active");
                           delayHighlight = undefined;
                           }
                        }
                     function outerMainClickHandler(event) {
                        if(event.button == 0) {
                           hideTools();
                           resetDelayHighlight();
                           }
                        }
                     function dummyNodeClickHandler(event) {
                        window.clearTimeout(changeTimeout);
                        if(noContentNode) {
                           try {
                              noContentNode.text("");
                              noContentNode.append("<br/>").children().selectNodes();
                              }
                           catch(e) {
                              }
                           noContentNode = null;
                           }
                        }
                     function replaceNodes(old, before) {
                        initialIdentifiedNodes.replaceElement(old, before);
                        for(var i = 0; i < markers.length; i++) {
                           var marker = markers[i];
                           marker.range.replaceElement(old, before);
                           }
                        }
                     var toolWindow;
                     function displayTool(options) {
                        if(toolWindow || options === undefined) {
                           hideToolWindow();
                           core.window.focus();
                           }
                        if(options !== undefined) {
                           if(options.offset == undefined) {
                              options.offset = 0;
                              }
                           var WINDOW_WIDTH = 490;
                           toolWindow = $(document.createElement("div")).addClass("tool-window").hover(function(event) {
                              event.stopPropagation(); }
                           , function() {
                              }
                           );
                           toolWindow.css(options.coords === undefined ? {
                              left : WINDOW_WIDTH + 2, top : 0, width : WINDOW_WIDTH - options.offset}
                           : options.coords);
                           var collapser = utility.createLink().addClass(options.closer ? "close-button" : "marker-button").appendTo(toolWindow);
                           if(options.closer) {
                              collapser.click(hideToolWindow);
                              }
                           else {
                              collapser.bind("click", {
                                 offset : options.offset}
                              , function(event) {
                                 event.stopPropagation(); var me = $(this); me.toggleClass("expand"); var expanded = me.hasClass("expand"); me.parent().stop().animate( {
                                    left : expanded ? WINDOW_WIDTH + 2 : 8 + event.data.offset}
                                 ); }
                              );
                              }
                           toolWindow.append(options.content.children()).appendTo(coreWrapper).click(function(event) {
                              event.stopPropagation(); }
                           );
                           if(options.handler !== undefined) {
                              toolWindow.closeHandler = options.handler;
                              }
                           toolWindow.closeOnClick = options.clickCloses;
                           var func;
                           if(options.noFocus === undefined ||!options.noFocus) {
                              func = function() {
                                 $(this).focusFirstEditable();
                                 }
                              }
                           if(options.coords === undefined) {
                              toolWindow.animate(options.coords === undefined ? {
                                 left : 8 + options.offset}
                              : options.coords, "normal", func);
                              }
                           else {
                              toolWindow.hide().fadeIn("normal", func);
                              }
                           return toolWindow;
                           }
                        }
                     function hideToolWindow() {
                        if(toolWindow) {
                           if(selectedMarker && selectedMarker.hasChanged) {
                              triggerChange();
                              }
                           if(toolWindow.closeHandler !== undefined) {
                              toolWindow.closeHandler.call();
                              }
                           toolWindow.fadeOut("normal", function() {
                              $(this).remove(); }
                           );
                           }
                        toolWindow = null;
                        }
                     function getTopics() {
                        return me.page.server.getTopics();
                        }
                     function getCard() {
                        return currentCard;
                        }
                     function openCardFromPlugin(card) {
                        mainView.openCard(card, card.step.topic.isEditable());
                        }
                     var isEditor;
                     var currentCallback;
                     function createRangeForMarker(marker, fromClipboard) {
                        var selection = $.getSelection( {
                           context : core.document, disableSurrounding :!isEditor, returnNullIfCollapsed : true}
                        );
                        if(selection) {
                           selection = selection.not("br");
                           }
                        if(selection && selection.length > 0) {
                           $.clearSelection(core.document);
                           marker.range = selection;
                           marker.isMine = true;
                           marker.user = me.page.server.getCurrentUser();
                           addMarker(marker);
                           if(!fromClipboard) {
                              selectMarker(marker);
                              }
                           triggerChange();
                           }
                        else {
                           if(!isEditor) {
                              if(selection) {
                                 $.clearSelection(core.document);
                                 }
                              setupMarkerHover(marker, "marker");
                              }
                           else {
                              alert(lang.SEL_REQUIRED_HIGHLIGHT);
                              }
                           }
                        }
                     var curHighlightStyle;
                     var mainHighlightButton;
                     function toolbarHandler(event) {
                        event.stopPropagation();
                        var button = event.data;
                        var action = button.action;
                        hideMenu();
                        if(action.substr(0, 7) == "heading") {
                           core.document.execCommand("FormatBlock", false, ($.browser.msie ? "Heading " : "h") + action.substring(8));
                           return;
                           }
                        switch(action) {
                           case"remove_item" : if(selectedMarker) {
                              copyToClipboard(selectedMarker);
                              removeMarker(selectedMarker);
                              triggerChange();
                              }
                           else {
                              alert(lang.NO_MARKER_FOR_DEL);
                              }
                           break;
                           case"empty_trash" : resetDelayHighlight();
                           client.pageplugins.NoteDisplay.markerClipboard.splice(0, client.pageplugins.NoteDisplay.markerClipboard.length);
                           updateBoxes();
                           break;
                           case"insertParagraph" : core.document.execCommand("FormatBlock", false, ($.browser.msie ? "Paragraph" : "p"));
                           break;
                           case"clearFont" : core.clearFormatting(utility.ClearFormattingConstants.removeStyle | utility.ClearFormattingConstants.cleanMarker);
                           clearFormattingHook(event);
                           break;
                           case"copy" : case"paste" : case"cut" : try {
                              core.document.execCommand(action, false, null);
                              }
                           catch(ex) {
                              alert(action == "copy" ? lang.COPY_SECURITY : (action == "cut" ? lang.CUT_SECURITY : lang.PASTE_SECURITY));
                              }
                           break;
                           case"save" : mainView.saveCard();
                           break;
                           case"revert" : if(confirm(lang.REVERT_CHANGES_TEXT)) {
                              me.revertChanges();
                              mainView.checkCardRevision();
                              }
                           break;
                           case"find" : alert(lang.FIND_HOW_TO);
                           break;
                           default : core.document.execCommand(action, false, null);
                           break;
                           }
                        }
                     this.revertChanges = function() {
                        if(loaded) {
                           unloadCard.call(this, true);
                           }
                        loadCard.call(this);
                        }
                     var markerInserted;
                     function addMarker(marker) {
                        marker.displayInterface = displayInterface;
                        hasChanged = true;
                        markers.push(marker);
                        markerInserted = true;
                        if(!isEditor || PERMANENT_HIGHLIGHTING) {
                           marker.adjustClass();
                           }
                        marker.load();
                        if(marker.startMarker) {
                           marker.startMarker.appendTo(markerBar).disableContextMenu();
                           }
                        if(marker.endMarker) {
                           marker.endMarker.appendTo(markerBar);
                           }
                        updateMarkers();
                        updateBoxes();
                        }
                     this.showMarkers = function(visible) {
                        $.fn[visible ? "fadeIn" : "fadeOut"].call(markerBar);
                        }
                     function getMarkers() {
                        return markers.clone();
                        }
                     this.getMarkerCount = function() {
                        return markers.length;
                        }
                     var suppressMarkerUpdate;
                     function updateMarkers() {
                        if(suppressMarkerUpdate) {
                           return;
                           }
                        if(isEditor) {
                           if(referenceMarker !== undefined) {
                              referenceMarker.range = core.body.children();
                              }
                           var torem;
                           for(var i = 0; i < markers.length; i++) {
                              var marker = markers[i];
                              if(!marker.range ||!marker.range.containsVisibleElements()) {
                                 copyToClipboard(marker);
                                 suppressMarkerUpdate = true;
                                 removeMarker(marker);
                                 suppressMarkerUpdate = false;
                                 i--;
                                 }
                              }
                           var allMarkerNodes = $(".marker", core.body);
                           for(var i = 0; i < markers.length; i++) {
                              var marker = markers[i];
                              allMarkerNodes = allMarkerNodes.not(marker.range.get());
                              }
                           allMarkerNodes.removeMarkerClass();
                           }
                        if(markers.length == 0) {
                           return;
                           }
                        var regions = [];
                        for(var i = 0; i < markers.length; i++) {
                           var marker = markers[i];
                           var updateArgs = new Object();
                           updateArgs.handled = false;
                           marker.update(updateArgs);
                           if(!updateArgs.handled) {
                              var outerElements = null;
                              var firstPos = null;
                              if(marker.startMarker && (isEditor || marker.range.containsVisibleElements())) {
                                 outerElements = marker.range.getFirstAndLast();
                                 var element = marker.startMarker.get(0);
                                 var relation = outerElements.first;
                                 firstPos = utility.getRelativePosition(relation, relation.ownerDocument.body);
                                 var startRegion = null;
                                 startRegion = {
                                    left : 0, top : firstPos.top, right : element.offsetWidth, bottom : firstPos.top + element.offsetHeight};
                                 correctRegion(startRegion, regions, 64);
                                 regions.push(startRegion);
                                 marker.startMarker.css( {
                                    top : startRegion.top, left : startRegion.left}
                                 );
                                 if(marker === selectedMarker && removeMarkerButton) {
                                    removeMarkerButton.css("top", startRegion.top);
                                    }
                                 }
                              }
                           }
                        return;
                        function correctRegion(region, regions, maxRight) {
                           var intersectionFound;
                           do {
                              intersectionFound = false;
                              for(var i = 0; i < regions.length; i++) {
                                 var compRegion = regions[i];
                                 if(compRegion && regionsIntersect(region, compRegion)) {
                                    var rightOffset = compRegion.right - region.left + 1;
                                    var topOffset = compRegion.bottom - region.top + 1;
                                    if(region.right + rightOffset > maxRight || topOffset < rightOffset) {
                                       region.top += topOffset;
                                       region.bottom += topOffset;
                                       region.right -= region.left;
                                       region.left = 0;
                                       }
                                    else {
                                       region.right += rightOffset;
                                       region.left += rightOffset;
                                       }
                                    intersectionFound = true;
                                    }
                                 }
                              }
                           while(intersectionFound);
                           return;
                           function regionsIntersect(region1, region2) {
                              var partsIn = {
                                 left : (region2.left >= region1.left) && (region2.left <= region1.right), right : (region2.right >= region1.left) && (region2.right <= region1.right), top : (region2.top >= region1.top) && (region2.top <= region1.bottom), bottom : (region2.bottom >= region1.top) && (region2.bottom <= region1.bottom), embedded : ((region1.left >= region2.left) && (region1.left <= region2.right) && (region1.top >= region2.top) && (region1.top <= region2.bottom))}
                              return((partsIn.left || partsIn.right) && (partsIn.top || partsIn.bottom)) || partsIn.embedded;
                              }
                           }
                        }
                     function updateBoxes() {
                        if(suppressMarkerUpdate) {
                           return;
                           }
                        if((client.pageplugins.NoteDisplay.markerClipboard.length > 0) != args.clipboardBox.hasClass("full")) {
                           args.clipboardBox.toggleClass("full");
                           }
                        var trainableCount = 0;
                        markers.each(function() {
                           if(this.trainable()) {
                              trainableCount++; }
                           }
                        );
                        args.flashCardBox.removeClass("full half");
                        if(trainableCount > 2) {
                           args.flashCardBox.addClass("full");
                           }
                        else if(trainableCount > 0) {
                           args.flashCardBox.addClass("half");
                           }
                        }
                     function removeMarker(marker) {
                        if(selectedMarker == marker) {
                           suppressMarkerUpdate = true;
                           unselectMarker();
                           suppressMarkerUpdate = false;
                           }
                        if(markers.indexOf(marker) >- 1) {
                           hasChanged = true;
                           markers.remove(marker);
                           removeMarkerCore(marker);
                           }
                        updateMarkers();
                        updateBoxes();
                        }
                     function removeMarkerCore(marker, norebuild) {
                        var start = marker.startMarker;
                        var end = marker.endMarker;
                        marker.unload();
                        marker.restore();
                        if(!isEditor || PERMANENT_HIGHLIGHTING) {
                           var nodes = marker.range;
                           if(!norebuild) {
                              for(var i = 0; i < markers.length; i++) {
                                 var otherMarker = markers[i];
                                 nodes = nodes.not(otherMarker.range.get());
                                 }
                              }
                           nodes.removeMarkerClass();
                           }
                        marker.range = null;
                        marker.displayInterface = null;
                        if(start) {
                           start.remove();
                           }
                        if(end) {
                           end.remove();
                           }
                        }
                     function copyToClipboard(marker) {
                        if(marker.trashable()) {
                           client.pageplugins.NoteDisplay.markerClipboard.push(marker.clone());
                           updateBoxes();
                           }
                        }
                     var removeMarkerButton;
                     var selectedMarker;
                     function selectMarker(marker) {
                        if(selectedMarker == marker) {
                           return;
                           }
                        unselectMarker();
                        if(!marker) {
                           return;
                           }
                        marker.range.addClass("highlight");
                        selectedMarker = marker;
                        marker.selected = true;
                        if(marker.startMarker && marker.startMarker.is("a")) {
                           marker.startMarker.get(0).focus();
                           }
                        marker.onSelect();
                        var currentUser = me.page.server.getCurrentUser();
                        if((isEditor || marker.user == currentUser) && marker.startMarker && marker.startMarker.parent().length > 0) {
                           removeMarkerButton = utility.createButton(marker.trashable() ? "user-trash-small.png" : "edit-delete-small.png", "remove_item", marker.trashable() ? lang.MOVE_TO_TRASH : lang.REMOVE_THIS, toolbarHandler).appendTo(markerBar);
                           removeMarkerButton.css( {
                              top : marker.startMarker.get(0).offsetTop, left : 70, width : 16, height : 16}
                           );
                           }
                        }
                     function unselectMarker() {
                        if(selectedMarker) {
                           selectedMarker.range.removeClass("highlight");
                           if(removeMarkerButton) {
                              removeMarkerButton.remove();
                              removeMarkerButton = null;
                              }
                           var marker = selectedMarker;
                           delete selectedMarker.selected;
                           selectedMarker = null;
                           marker.onDeselect();
                           if(marker.hasChanged) {
                              updateBoxes();
                              triggerChange();
                              }
                           updateMarkers();
                           }
                        }
                     var currentNodeRange;
                     var currentTask;
                     function setupMarkerHover(data, task) {
                        var thisis = $(this);
                        curEl = null;
                        currentTask = task;
                        currentNodeRange = $("*", core.body);
                        currentNodeRange.bind("mouseover", nodeOver).bind("mouseout", nodeOut).bind("click", data, nodeClick);
                        currentTool = thisis;
                        }
                     var curEl;
                     function nodeOver(event) {
                        if(!curEl) {
                           curEl = $(this).addClass("pointer highlight");
                           }
                        }
                     function nodeOut(event) {
                        if(curEl) {
                           curEl.removeClass("pointer highlight");
                           curEl = null;
                           }
                        }
                     function nodeClick(event) {
                        if(currentTask == "marker") {
                           event.stopPropagation();
                           event.preventDefault();
                           var marker = event.data;
                           marker.range = $(this);
                           resetNodeHover();
                           marker.isMine = true;
                           marker.user = me.page.server.getCurrentUser();
                           addMarker(marker);
                           selectMarker(marker);
                           triggerChange();
                           }
                        }
                     function resetNodeHover() {
                        nodeOut();
                        if(currentNodeRange) {
                           currentNodeRange.unbind("click", nodeClick).unbind("mouseover", nodeOver).unbind("mouseout", nodeOut);
                           currentNodeRange = null;
                           }
                        currentTask = null;
                        curEl = null;
                        }
                     function getMarkerBar() {
                        return markerBar;
                        }
                     function getMarkerBarOffset(element) {
                        return utility.getRelativeOffset(element, markerBar[0]);
                        }
                     function createMarkerMenuEntry(label, icon, marker) {
                        if(label == "-") {
                           return utility.createMenuItemSeparator().appendTo(menu);
                           }
                        var entry = utility.createMenuItem(icon, "", label, markerMenuItemHandler);
                        entry.tag = marker;
                        entry.appendTo(menu);
                        return entry;
                        }
                     function createPluginMenuEntry(label, icon, handler) {
                        if(label == "-") {
                           return utility.createMenuItemSeparator().appendTo(menu);
                           }
                        var entry = utility.createMenuItem(icon, "", label, pluginMenuItemHandler, handler);
                        entry.appendTo(menu);
                        return entry;
                        }
                     function pluginMenuItemHandler(event) {
                        event.stopPropagation();
                        var handler = event.data;
                        delete event.data;
                        handler.call(this, event);
                        hideMenu();
                        }
                     function markerMenuItemHandler(event) {
                        event.stopPropagation();
                        var entry = event.data;
                        var marker = entry.tag;
                        delete entry.tag;
                        if(entry.fromClipboard) {
                           if(client.pageplugins.NoteDisplay.markerClipboard.indexOf(marker) !=- 1) {
                              client.pageplugins.NoteDisplay.markerClipboard.remove(marker);
                              }
                           }
                        hideMenu();
                        resetDelayHighlight();
                        var insertingArgs = new Object();
                        insertingArgs.cancel = false;
                        marker.loading(insertingArgs);
                        if(insertingArgs.cancel) {
                           return;
                           }
                        createRangeForMarker(marker, entry.fromClipboard === true);
                        }
                     function mainClickHandler(event) {
                        if(event === undefined || event.button == 0) {
                           for(var i = 0; i < registeredPlugins.length; i++) {
                              var registeredPlugin = registeredPlugins[i];
                              registeredPlugin.onMainClick();
                              }
                           if(!isEditor) {
                              unselectMarker();
                              }
                           if(delayHighlight === undefined) {
                              hideMenu();
                              }
                           if(args.mainClickHandler !== undefined) {
                              args.mainClickHandler.call(me);
                              }
                           if(toolWindow && toolWindow.closeOnClick) {
                              hideToolWindow()}
                           }
                        }
                     var imagesCount;
                     function imageLoadedHandler(event) {
                        imagesCount--;
                        if(imagesCount <= 0) {
                           checkDisplayHeight();
                           updateMarkers();
                           }
                        }
                     function hideTools() {
                        unselectMarker();
                        hideMenu();
                        resetNodeHover();
                        hideToolWindow();
                        }
                     var timesChanged;
                     function checkChangeBalloon() {
                        if(!currentBalloon ||!currentBalloon.is(":visible")) {
                           timesChanged++;
                           }
                        else {
                           return;
                           }
                        }
                     function explainClipboard(after) {
                        currentBalloon = $.showBalloon(clipboardButton, lang.BALLOON_TEXTS.DESKTOP.CLIPBOARD_EXPLANATION, jQuery.balloon.BalloonPositionConstants.topLeft, undefined, balloonConfig, "clipboard", after);
                        return currentBalloon;
                        }
                     function explainHighlighterPen(after) {
                        currentBalloon = $.showBalloon(args.pen, lang.BALLOON_TEXTS.DESKTOP.HIGHLIGHTER_EXPLANATION, jQuery.balloon.BalloonPositionConstants.bottomLeft, undefined, balloonConfig, "highlighterclick", after, true);
                        return currentBalloon;
                        }
                     function changeHandler(event, orgEvent) {
                        triggerChangeHandler();
                        if(orgEvent === undefined || orgEvent.type == "keypress") {
                           triggerChange();
                           }
                        }
                     var referenceMarker;
                     var sizeWarningShown;
                     function handleChange() {
                        if(!loaded) {
                           return;
                           }
                        if(isEditor) {
                           checkChangeBalloon();
                           }
                        for(var i = 0; i < registeredPlugins.length; i++) {
                           var registeredPlugin = registeredPlugins[i];
                           registeredPlugin.onChange();
                           }
                        updateMarkers();
                        if(initiallyEmpty && config.reference !== undefined && (config.reference.auto || config.reference.link) && client.markers.ReferenceMarker !== undefined) {
                           var referenceFound = false;
                           markers.each(function() {
                              if(this.tool == "reference") {
                                 referenceFound = true; }; }
                           )if(!referenceFound) {
                              var linkRelation;
                              if(config.reference.auto === true) {
                                 if(window.parent.mbGetUrl !== undefined && window.parent.mbGetTitle !== undefined) {
                                    var url = window.parent.mbGetUrl();
                                    var title = window.parent.mbGetTitle();
                                    if(title === undefined) {
                                       title = url;
                                       }
                                    if(url !== undefined) {
                                       linkRelation = new client.data.LinkRelation( {
                                          title : title, link : url}
                                       );
                                       }
                                    }
                                 }
                              else {
                                 linkRelation = new client.data.LinkRelation( {
                                    title : config.reference.title, link : config.reference.link}
                                 );
                                 }
                              if(linkRelation !== undefined) {
                                 referenceMarker = new client.markers.ReferenceMarker();
                                 referenceMarker.hasChanged = true;
                                 referenceMarker.range = core.body.children();
                                 referenceMarker.relations.push(linkRelation);
                                 addMarker(referenceMarker);
                                 initiallyEmpty = false;
                                 }
                              }
                           }
                        }
                     var dropTimer;
                     function pasteHandler(event) {
                        dropTimer = window.setTimeout(dropHandler, 300);
                        }
                     function ensureBodyBlocks() {
                        var nodeCache = [];
                        var blockTags = ["p", "h1", "h2", "h3", "h4", "h5", "h6", "pre", "br", "ul", "ol", "table"];
                        core.body.contents().each(function() {
                           if(this.nodeType == 1) {
                              var tagName = this.tagName.toLowerCase(); if(blockTags.indexOf(tagName) >- 1) {
                                 if(nodeCache.length > 0) {
                                    wrapCache(); nodeCache = []; }
                                 if(tagName == "br") {
                                    $(this).remove(); }
                                 }
                              else {
                                 nodeCache.push(this); }
                              }
                           else if(this.nodeType == 3) {
                              nodeCache.push(this); }
                           }
                        );
                        wrapCache();
                        function wrapCache() {
                           if(nodeCache.length > 0) {
                              if(nodeCache.length == 1 && nodeCache[0].nodeType == 3 && jQuery.trim(nodeCache[0].nodeValue).length == 0) {
                                 $(nodeCache[0]).remove();
                                 }
                              else {
                                 var p = $(core.document.createElement("p"));
                                 p.insertBefore(nodeCache[0]);
                                 p.append(nodeCache);
                                 }
                              }
                           }
                        }
                     function dropHandler(event) {
                        if(noContentNode) {
                           dummyNodeClickHandler();
                           }
                        ensureBodyBlocks();
                        core.clearFormatting(utility.ClearFormattingConstants.cleanServer | utility.ClearFormattingConstants.cleanMarker | utility.ClearFormattingConstants.removeNoAttrSpan, core.body);
                        handleChange();
                        triggerChange();
                        mainClickHandler.call(this, event);
                        }
                     function checkDisplayHeight() {
                        var height, newHeight;
                        var scrollHeight = core.body[0].scrollHeight;
                        var mainHeight = mainContainer.height();
                        height = core.body[0].offsetHeight;
                        var restoreFrameHeight = false;
                        if($.browser.msie) {
                           core.frame.css("height", "auto");
                           scrollHeight = core.body[0].scrollHeight;
                           restoreFrameHeight = true;
                           }
                        else {
                           scrollHeight = height;
                           }
                        newHeight = (scrollHeight < mainHeight) ? mainHeight : scrollHeight;
                        if(restoreFrameHeight || height != newHeight || newHeight != mainHeight) {
                           core.frame.css("height", newHeight);
                           }
                        if(height != newHeight || newHeight != mainHeight) {
                           coreWrapper.css("height", newHeight);
                           markerBar.css("height", newHeight);
                           if(isEditor) {
                              if(newHeight > mainHeight) {
                                 if(!sizeWarningShown) {
                                    var item = mainView.queueInfo(lang.BALLOON_TEXTS.EDITOR.TOO_MUCH_CONTENT);
                                    var imgButtonRight = $(document.createElement("img")).attr("src", config.imagePath + "desktop-button-right.png").addClass("like-button");
                                    $("p.add-hint", item).before(imgButtonRight);
                                    sizeWarningShown = true;
                                    }
                                 }
                              else {
                                 if(sizeWarningShown) {
                                    mainView.clearInfos();
                                    sizeWarningShown = false;
                                    }
                                 }
                              }
                           }
                        }
                     function setConfirmUnload(on) {
                        if($.browser.msie) {
                           return;
                           }
                        window.onbeforeunload = (on) ? unloadFrameHandler : null;
                        }
                     function unloadFrameHandler(event) {
                        if(!hasChanged) {
                           return;
                           }
                        if(event) {
                           event.returnValue = lang.CONFIRM_CLOSE;
                           }
                        return lang.CONFIRM_CLOSE;
                        }
                     var menuButton, menu;
                     var delayHighlight;
                     function menuHandler(event) {
                        event.stopPropagation();
                        var node = this;
                        var button = $(this);
                        var action = event.data.action;
                        if((menuButton !== undefined && menuButton.index(this) >- 1) && action != "clipboard_menu") {
                           hideMenu();
                           return;
                           }
                        hideMenu();
                        var pos = {
                           left : node.offsetLeft, top : node.offsetTop};
                        pos.top += node.offsetHeight;
                        menu = utility.createMenu();
                        if(action == "formatpar") {
                           button.addClass("active");
                           utility.createMenuItem("", "insertParagraph", lang.PARA, toolbarHandler).appendTo(menu);
                           utility.createMenuItem("", "headingH1", lang._H1, toolbarHandler).appendTo(menu);
                           utility.createMenuItem("", "headingH2", lang._H2, toolbarHandler).appendTo(menu);
                           utility.createMenuItem("", "headingH3", lang._H3, toolbarHandler).appendTo(menu);
                           utility.createMenuItem("", "clearFont", lang.RESET_FONT, toolbarHandler).appendTo(menu);
                           utility.createMenuItemSeparator().appendTo(menu);
                           utility.createMenuItem("format-justify-left.png", "justifyLeft", lang.JUSTIFY_LEFT, toolbarHandler).appendTo(menu);
                           utility.createMenuItem("format-justify-center.png", "justifyCenter", lang.JUSTIFY_CENTER, toolbarHandler).appendTo(menu);
                           utility.createMenuItem("format-justify-right.png", "justifyRight", lang.JUSTIFY_RIGHT, toolbarHandler).appendTo(menu);
                           utility.createMenuItem("format-justify-fill.png", "justifyFull", lang.JUSTIFY_FULL, toolbarHandler).appendTo(menu);
                           utility.createMenuItemSeparator().appendTo(menu);
                           utility.createMenuItem("format-indent-less.png", "outdent", lang.OUTDENT, toolbarHandler).appendTo(menu);
                           utility.createMenuItem("format-indent-more.png", "indent", lang.INDENT, toolbarHandler).appendTo(menu);
                           }
                        else if(action == "highlight_menu" || action == "clipboard_menu" || action == "clipboard_manage") {
                           var isHighlight = (action == "highlight_menu");
                           var isManage = (action == "clipboard_manage");
                           if(!currentCard) {
                              return;
                              }
                           if(!isHighlight) {
                              pos.top -= node.offsetHeight;
                              }
                           hideBalloon();
                           if(!isEditor && currentCard.content == null) {
                              alert(lang.NO_HIGHLIGHT_IN_EMPTY_CARD);
                              return;
                              }
                           if(!isHighlight && client.pageplugins.NoteDisplay.markerClipboard.length == 0) {
                              if(explainClipboard() == null) {
                                 alert(lang.TRASH_EMPTY);
                                 }
                              return;
                              }
                           if(noContentNode) {
                              dummyNodeClickHandler();
                              alert(isHighlight ? lang.NO_CONTENT_FOR_HIGHLIGHT : lang.NO_CONTENT_FOR_REINSERT);
                              core.window.focus();
                              return;
                              }
                           var hasSelection = $.hasSelection(core.document);
                           if(!hasSelection) {
                              resetDelayHighlight();
                              if(!isManage) {
                                 button.addClass("active");
                                 delayHighlight = button;
                                 if(!$.browser.msie) {
                                    core.body.addClass("highlight-cursor");
                                    }
                                 if(isHighlight) {
                                    explainHighlighterPen();
                                    }
                                 return;
                                 }
                              else {
                                 button.removeClass("active");
                                 }
                              }
                           pos.right = 28;
                           delete pos.left;
                           if(isHighlight) {
                              if(!isEditor) {
                                 var selection = $.getSelection( {
                                    context : core.document, disableSurrounding : true, returnNullIfCollapsed : true}
                                 );
                                 if(selection) {
                                    selection.selectNodes();
                                    }
                                 }
                              registeredMarkers.each(function() {
                                 this.createMenuItems(createMarkerMenuEntry); }
                              );
                              }
                           else {
                              $(document.createElement("span")).text(hasSelection ? lang.INSERT_FROM_TRASH : lang.MANAGE_TRASH).css("padding-top", 0).appendTo(menu).css("height", 16);
                              if(hasSelection) {
                                 if(client.pageplugins.NoteDisplay.markerClipboard.length == 1) {
                                    var marker = client.pageplugins.NoteDisplay.markerClipboard[0];
                                    markerMenuItemHandler( {
                                       stopPropagation : function() {
                                          }
                                       , data : {
                                          fromClipboard : true, tag : marker}
                                       }
                                    );
                                    return;
                                    }
                                 client.pageplugins.NoteDisplay.markerClipboard.each(function() {
                                    this.displayInterface = displayInterface; var entry = this.createTrashMenuItem(); this.displayInterface = null; if(entry !== null) {
                                       var item = createMarkerMenuEntry(entry.label, entry.icon, this); item.fromClipboard = true; }
                                    }
                                 );
                                 utility.createMenuItemSeparator().appendTo(menu);
                                 }
                              utility.createMenuItem("user-trash-full.png", "empty_trash", lang.EMPTY_TRASH, toolbarHandler).appendTo(menu);
                              pos.bottom = node.offsetParent.offsetHeight - pos.top;
                              delete pos.top;
                              }
                           }
                        else if(action == "insert_menu") {
                           button.addClass("active");
                           if(noContentNode) {
                              dummyNodeClickHandler();
                              }
                           hideBalloon();
                           for(var i = 0; i < registeredPlugins.length; i++) {
                              var plugin = registeredPlugins[i];
                              plugin.loadMenuCreation(createPluginMenuEntry);
                              }
                           pos.right = node.offsetParent.offsetWidth - pos.left - node.offsetWidth;
                           delete pos.left;
                           }
                        else if(action == "editpar") {
                           button.addClass("active");
                           utility.createMenuItem("edit-cut.png", "cut", lang.CUT, toolbarHandler).appendTo(menu);
                           utility.createMenuItem("edit-copy.png", "copy", lang.COPY, toolbarHandler).appendTo(menu);
                           utility.createMenuItem("edit-paste.png", "paste", lang.PASTE, toolbarHandler).appendTo(menu);
                           utility.createMenuItemSeparator().appendTo(menu);
                           utility.createMenuItem("edit-find.png", "find", lang.FIND, toolbarHandler).appendTo(menu);
                           }
                        menu.css(pos).appendTo(button.parent()).focusFirstEditable();
                        menuButton = button;
                        event.preventDefault();
                        }
                     function hideBalloon() {
                        if(currentBalloon) {
                           currentBalloon.remove();
                           currentBalloon = null;
                           }
                        }
                     function hideMenu() {
                        if(menuButton) {
                           menuButton.removeClass("active");
                           menuButton = undefined;
                           }
                        if(menu) {
                           menu.remove();
                           menu = null;
                           }
                        }
                     this.checkTrainable = function() {
                        return client.Marker.checkTrainable(markers);
                        }
                     var lastSelection;
                     function unloadCard(forgetChanges) {
                        if(!loaded) {
                           return;
                           }
                        for(var i = 0; i < registeredPlugins.length; i++) {
                           var registeredPlugin = registeredPlugins[i];
                           if(registeredPlugin.displayInterface) {
                              registeredPlugin.unload();
                              }
                           registeredPlugin.displayInterface = null;
                           }
                        window.clearTimeout(saveTimeout);
                        saveTimeout = undefined;
                        if(noContentNode) {
                           noContentNode.remove();
                           $(core.document).unbind("click", dummyNodeClickHandler);
                           }
                        hideTools();
                        updateMarkers();
                        referenceMarker = undefined;
                        core.body.unbind("change", changeHandler);
                        $(core.body).unbind("paste", pasteHandler);
                        if($.browser.msie) {
                           $(core.body).unbind("drop", dropHandler);
                           }
                        else {
                           $(core.document).unbind("dragdrop", dropHandler);
                           }
                        $(core.document).unbind("mouseup", editorMouseUpHandler);
                        $(core.document).unbind("click", mainClickHandler);
                        $(document).unbind("click", outerMainClickHandler);
                        if(forgetChanges !== true) {
                           saveCard();
                           }
                        markers.each(function() {
                           removeMarkerCore(this, true); }
                        );
                        currentCard.studied = true;
                        if(isEditor) {
                           lastSelection = jQuery.storeSelection( {
                              context : core.document}
                           );
                           if(lastSelection !== undefined) {
                              lastSelection.card = currentCard;
                              }
                           initialIdentifiedNodes.empty();
                           initialIdentifiedNodes = null;
                           }
                        loaded = false;
                        markers = null;
                        }
                     function saveCard() {
                        window.clearTimeout(saveTimeout);
                        saveTimeout = undefined;
                        if(isEditor) {
                           core.fixListNesting();
                           ensureBodyBlocks();
                           core.clearFormatting(utility.ClearFormattingConstants.cleanServer | utility.ClearFormattingConstants.removeNoAttrSpan | utility.ClearFormattingConstants.removeEmptySpan | utility.ClearFormattingConstants.cleanMarker, core.body);
                           var allInitialNodes = initialIdentifiedNodes.get();
                           $("*", core.body).each(function() {
                              if((this.nodeType == 1) && (this.getAttribute("id") == null || this.getAttribute("id") == "" || allInitialNodes.indexOf(this) ==- 1)) {
                                 this.setAttribute("id", utility.getFreeName(this.ownerDocument)); }
                              }
                           );
                           }
                        data.markers = [];
                        for(var i = 0; i < markers.length; i++) {
                           var marker = markers[i];
                           hasChanged = hasChanged || marker.hasChanged;
                           if(marker.hasChanged ||!marker.id) {
                              var markerData = marker.transformStatic(true);
                              data.markers.push(markerData);
                              }
                           }
                        data.deletedMarkerIds = [];
                        currentCard.getMarkers().each(function() {
                           var remoteMarker = this; var found = false; markers.each(function() {
                              if(this === remoteMarker) {
                                 found = true; return false; }
                              }
                           ); if(!found) {
                              data.deletedMarkerIds.push(this.id); }
                           }
                        );
                        if(isEditor) {
                           unselectMarker();
                           if($("*", core.body).filter(":not(br):not(p)").length == 0 && core.body.text() == "") {
                              data.content = null;
                              currentCard.edited = false;
                              }
                           else {
                              var clone;
                              if($.browser.msie) {
                                 clone = core.body;
                                 }
                              else {
                                 clone = core.body.clone(false);
                                 }
                              $(".marker", clone).removeMarkerClass();
                              data.content = clone.html();
                              if($.browser.msie) {
                                 markers.each(function() {
                                    this.adjustClass(); }
                                 );
                                 }
                              }
                           initialIdentifiedNodes = $("[id]", core.body);
                           }
                        }
                     function unloadCore() {
                        if($.browser.mozilla) {
                           utility.mozilla.removeEnterKeyHandler(core.document);
                           }
                        setConfirmUnload(false);
                        core.unload();
                        core.destroy();
                        core = null;
                        }
                     var hasChanged;
                     this.hasChanged = function() {
                        return hasChanged;
                        }
                     var changeTimeout;
                     function triggerChangeHandler() {
                        if(noContentNode) {
                           dummyNodeClickHandler();
                           }
                        window.clearTimeout(changeTimeout);
                        changeTimeout = window.setTimeout(handleChange, 300);
                        checkDisplayHeight();
                        }
                     function triggerChange() {
                        hasChanged = true;
                        triggerAutoSave();
                        }
                     var saveTimeout;
                     function triggerAutoSave() {
                        if(loaded) {
                           window.clearTimeout(saveTimeout);
                           saveTimeout = window.setTimeout(saveTimeoutHandler, SAVE_TIMEOUT * 1000);
                           }
                        }
                     function saveTimeoutHandler() {
                        if(loaded &&!editingTitle && (noContentNode === undefined || noContentNode == null) && toolWindow == null && delayHighlight === undefined &&!$.hasSelection(core.document)) {
                           mainView.saveCard();
                           }
                        else {
                           window.clearTimeout(saveTimeout);
                           saveTimeout = window.setTimeout(saveTimeoutHandler, SAVE_RETIMEOUT * 1000);
                           }
                        }
                     this.getChanges = function() {
                        return data;
                        }
                     this.resetChanges = function() {
                        data = {
                           };
                        hasChanged = false;
                        }
                     this.unload = function() {
                        window.clearTimeout(changeTimeout);
                        changeTimeout = undefined;
                        window.clearTimeout(saveTimeout);
                        saveTimeout = undefined;
                        window.clearTimeout(dropTimer);
                        dropTimer = undefined;
                        client.pageplugins.NoteDisplay.markerClipboard = [];
                        hideBalloon();
                        this.closeCard();
                        unloadCore();
                        registeredMarkers.each(function() {
                           this.displayInterface = null; }
                        );
                        keyHooks = undefined;
                        displayInterface.destroy();
                        displayInterface = null;
                        if(allElements) {
                           allElements.remove();
                           allElements.empty();
                           }
                        highlightButton.removeAttr("accesskey");
                        highlightButton.unbind("click", menuHandler).unbind("contextmenu", menuHandler);
                        clipboardButton.unbind("click", menuHandler).unbind("contextmenu", menuHandler);
                        allElements/*=insertButton*/
                        = highlightButton = categoryLabel = toolbar = markerBar = null;
                        me = null;
                        markers = null;
                        content = null;
                        currentCard = null;
                        client.PagePlugin.prototype.unload.call(this);
                        }
                     }
                  client.pageplugins.NoteDisplay.prototype = new client.PagePlugin;
                  client.pageplugins.NoteDisplay.DISPLAY_WIDTH = 488;
                  client.pageplugins.NoteDisplay.DISPLAY_HEIGHT = 316;
                  client.pageplugins.NoteDisplay.markerClipboard = [];
                  client.pageplugins.Search = function(args) {
                     client.PagePlugin.call(this);
                     var searchAndImportResources;
                     var searchMyTopicsResources;
                     var wikipediaProvider;
                     var mediabirdTopicProvider;
                     var mediabirdCardProvider;
                     this.load = function(page) {
                        me = this;
                        mediabirdTopicProvider = new client.widgets.MediabirdSearchProvider( {
                           server : me.page.server, defaultType : client.widgets.MediabirdSearchProviderType.topic}
                        );
                        mediabirdTopicProvider.name = lang.TOPIC;
                        mediabirdTopicProvider.order = 1;
                        mediabirdCardProvider = new client.widgets.MediabirdSearchProvider( {
                           server : me.page.server, defaultType : client.widgets.MediabirdSearchProviderType.card}
                        );
                        mediabirdCardProvider.name = lang.NOTE_SHEET;
                        mediabirdCardProvider.order = 2;
                        wikipediaProvider = new client.widgets.WikipediaSearchProvider();
                        wikipediaProvider.order = 10;
                        searchMyTopicsResources = {
                           heading : lang.SEARCH_MY_TOPICS_HEADER, emptyHTML : lang.NO_RESULTS_OF_MY_TOPIC_SEARCH_HTML, introHTML : lang.FIND_MY_TOPIC_HTML, refreshTopics : true};
                        searchAndImportResources = {
                           heading : lang.SEARCH_ARTICLES_HEADER, emptyHTML : lang.NO_RESULTS_HTML, introHTML : lang.FIND_SEARCH_HTML};
                        }
                     this.handleButtonClick = function() {
                        var showAll = (filterSearch === undefined);
                        showView(searchMyTopicsResources, [mediabirdTopicProvider]);
                        if(showAll) {
                           filterSearch.search();
                           }
                        }
                     var mainView;
                     this.onPluginLoad = function(plugin) {
                        if(plugin instanceof client.pageplugins.MainView) {
                           mainView = plugin;
                           }
                        }
                     this.getPreview = function() {
                        var content = $(document.createElement("div"));
                        if(mainView !== undefined) {
                           showSearchBar(content, lang.SEARCH_MY_TOPICS_HEADER, "applications-office-small.png", {
                              providers : [mediabirdTopicProvider], resources : searchMyTopicsResources}
                           );
                           showSearchBar(content, lang.SEARCH_ARTICLES_HEADER, "wikipedia-small.png", {
                              providers : [mediabirdCardProvider, wikipediaProvider], resources : searchAndImportResources}
                           );
                           }
                        return content;
                        }
                     function showSearchBar(container, title, icon, searchObj) {
                        $(document.createElement("h3")).text(title).appendTo(container);
                        var toolbar = utility.createToolbar().addClass("noclear");
                        $(document.createElement("img")).attr("src", config.imagePath + icon).addClass("left icon small").appendTo(toolbar);
                        var searchField = utility.createInput(lang.SEARCH);
                        var searchGroupButton = utility.createMenuItem("go-next.png", null, lang.GO);
                        searchObj.searchField = searchField;
                        searchGroupButton.bind("click", searchObj, searchGoHandler);
                        searchField.bind("keypress", searchObj, searchGoHandler);
                        utility.createLink(lang.SEARCH_MORE).appendTo(container).addClass("more margin").bind("click", searchObj, function(event) {
                           showView(event); if(event.data.providers.indexOf(mediabirdTopicProvider) >- 1) {
                              filterSearch.search(); }
                           }
                        );
                        toolbar.append(searchField).append(searchGroupButton).appendTo(container);
                        }
                     var filterSearch;
                     function searchGoHandler(event) {
                        if(event.type == "keypress" && event.keyCode != 13) {
                           return;
                           }
                        if(showView(event.data.resources, event.data.providers)) {
                           var field = event.data.searchField;
                           if(!field.hasClass("grey")) {
                              filterSearch.search(field.val());
                              }
                           }
                        }
                     var createTopicToolbar;
                     function showView(resources, providers) {
                        if(resources.data !== undefined) {
                           providers = resources.data.providers;
                           resources = resources.data.resources;
                           }
                        if(resources.refreshTopics) {
                           refreshGroupsAndTopics();
                           }
                        var container;
                        if(mainView !== undefined) {
                           container = mainView.requestContainer(client.pageplugins.MainViewToolConstants.searchView, closeHandler);
                           if(!container) {
                              return false;
                              }
                           }
                        else {
                           return false;
                           }
                        heading = $(document.createElement("h1"));
                        heading.text(resources.heading).appendTo(container);
                        filterSearch = new client.widgets.FilterSearch( {
                           container : container, resultClickHandler : resultClickHandler, emptyHTML : resources.emptyHTML, introHTML : resources.introHTML, providers : providers, searchFinishedHandler : searchFinishedHandler, showCheckBoxes : providers.indexOf(wikipediaProvider) ==- 1}
                        );
                        createTopicToolbar = utility.createToolbar().hide();
                        if(providers.indexOf(wikipediaProvider) ==- 1 &&!config.reduceFeatureSet) {
                           utility.createMenuItem("edit-delete.png", null, lang.DELETE, deleteTopicHandler).attr("title", lang.DELETE_TOPICS).appendTo(createTopicToolbar);
                           utility.createMenuItem("wikipedia.png", null, lang.SEARCH_WIKIPEDIA, showWikiHandler).addClass("right").appendTo(createTopicToolbar);
                           }
                        utility.createMenuItem("create-map.png", null, lang.CREATE_NEW_TOPIC, createTopicHandler).addClass("right").attr("title", lang.CREATE_NEW_TOPIC_EXPLANATION).appendTo(createTopicToolbar);
                        createTopicToolbar.appendTo(container);
                        return true;
                        }
                     function showWikiHandler() {
                        var term = filterSearch.getLastTerm();
                        showView(searchAndImportResources, [mediabirdCardProvider, wikipediaProvider]);
                        if(term !== undefined && term.length > 0) {
                           filterSearch.search(term);
                           }
                        }
                     function createTopicHandler() {
                        mainView.createTopic(filterSearch.getLastTerm());
                        mainView.hideView();
                        }
                     function getOwnCheckedTopics() {
                        var checkedItems = filterSearch.getCheckedItems();
                        checkedItems.clone().each(function() {
                           if(this.object.access != client.data.TopicAccessConstants.owner) {
                              checkedItems.remove(this); }
                           }
                        );
                        return checkedItems}
                     function deleteTopicHandler(event) {
                        var checkedItems = getOwnCheckedTopics();
                        if(checkedItems.length > 0) {
                           var names = [];
                           var topics = [];
                           checkedItems.each(function() {
                              if(this.object instanceof client.data.Topic) {
                                 names.push(this.title); topics.push(this.object); }
                              }
                           );
                           if(confirm(lang.DELETE_TOPICS_CONFIRM.replace("####", names.join(", ")))) {
                              me.page.server.deleteTopics(topics, utility.createCallback(me, deleteTopicCallback));
                              }
                           }
                        else {
                           alert(lang.CHECK_TOPICS_TO_DELETE);
                           }
                        }
                     function deleteTopicCallback(data) {
                        if(data.success) {
                           if(filterSearch !== undefined) {
                              var items = filterSearch.getItems();
                              data.deletedIds.each(function() {
                                 var topicId = this; items.each(function() {
                                    if(this.object.id == topicId) {
                                       filterSearch.removeItem(this); return false; }
                                    }
                                 ); }
                              );
                              }
                           }
                        else {
                           alert(lang.COULD_NOT_DELETE_TOPICS);
                           }
                        }
                     var ALWAYS_SHOW_CREATE = true;
                     function searchFinishedHandler(items) {
                        if(createTopicToolbar !== undefined) {
                           $.fn[(items.length == 0 || ALWAYS_SHOW_CREATE) ? "fadeIn" : "fadeOut"].call(createTopicToolbar);
                           }
                        }
                     var lastRefresh;
                     function refreshGroupsAndTopics() {
                        var end = new Date();
                        if(lastRefresh === undefined || (end.getTime() - lastRefresh.getTime() > 10000)) {
                           me.page.server.loadTopicList(utility.createCallback());
                           me.page.server.loadGroupList(utility.createCallback());
                           lastRefresh = end;
                           }
                        }
                     function closeHandler() {
                        createTopicToolbar.remove();
                        createTopicToolbar = undefined;
                        filterSearch.destroy();
                        filterSearch = undefined;
                        }
                     var frame;
                     function resultClickHandler(event) {
                        var obj = event.data.object;
                        var info = event.data;
                        if(obj instanceof client.data.Topic) {
                           mainView.openTopic(obj, obj.isEditable());
                           mainView.hideView();
                           }
                        else if(obj instanceof client.data.Group) {
                           me.page.server.joinGroup(obj, utility.createCallback(me, function(data) {
                              if(data.success) {
                                 if(data.state == 1) {
                                    if(info.topicId !== undefined) {
                                       var topicId = info.topicId; this.page.server.loadTopicList(utility.createCallback(this, function(data) {
                                          if(data.success) {
                                             var topics = this.page.server.getTopics(); topics.each(function() {
                                                if(this.id == topicId) {
                                                   mainView.openTopic(this); mainView.hideView(); return false; }
                                                }
                                             )}
                                          }
                                       )); }
                                    }
                                 else {
                                    alert(lang.GROUP_REQUESTED_FROM_TOPIC); }
                                 }
                              }
                           ));
                           }
                        else if(obj instanceof client.data.Card) {
                           if(obj.needsLoading) {
                              me.page.server.loadCardContents(obj, utility.createCallback(me, cardLoadCallback));
                              }
                           else {
                              showCard(obj);
                              }
                           }
                        else if(obj instanceof client.data.WebResult) {
                           var floatObj = mainView.requestFloat(researchCloseHandler, true, true, true, true);
                           var topic = mainView.getTopic();
                           $(document.createElement("h3")).text((topic === undefined ||!topic.isEditable()) ? lang.VIEW_RESULT_EXPLANATION : lang.DRAG_TO_LEFT_EXPLANATION).appendTo(floatObj.content);
                           frame = $(document.createElement("iframe")).attr("src", obj.url);
                           frame.css( {
                              width : config.RESEARCH_PANEL_WIDTH, height : 320, border : "0 none"}
                           );
                           if($.browser.msie) {
                              frame.attr("frameBorder", "0");
                              }
                           utility.researchReference = {
                              link : obj.url, title : obj.title};
                           floatObj.content.append(frame);
                           }
                        }
                     function cardLoadCallback(data) {
                        if(data.success) {
                           showCard(data.cards[0]);
                           }
                        else {
                           alert(lang.COULD_NOT_LOAD_CARD);
                           }
                        }
                     function showCard(obj) {
                        var floatObj = mainView.requestFloat(researchCloseHandler, true, true, true, true);
                        var wrapper = $(document.createElement("div")).css( {
                           "overflow-y" : "auto", "width" : config.RESEARCH_PANEL_WIDTH, "height" : 320}
                        );
                        var display = $(document.createElement("div")).html(obj.content).appendTo(wrapper);
                        $("a", display).attr("target", "_blank");
                        $("a.cardlink", display).removeAttr("href");
                        utility.researchReference = {
                           link : client.data.Card.LINK_PREFIX + obj.id, title : obj.title};
                        $(document.createElement("h3")).text(lang.DRAG_TO_LEFT_EXPLANATION).appendTo(floatObj.content);
                        floatObj.content.append(wrapper);
                        }
                     function researchCloseHandler(event) {
                        frame === undefined;
                        delete utility.researchReference;
                        }
                     this.unload = function() {
                        wikipediaProvider.destroy();
                        mediabirdTopicProvider.destroy();
                        mediabirdCardProvider.destroy();
                        }
                     }
                  client.pageplugins.Search.prototype = new client.PagePlugin;
                  client.pageplugins.NoteDisplayInterface = function() {
                     this.destroy();
                     }
                  client.pageplugins.NoteDisplayInterface.prototype.isEditor = false;
                  client.pageplugins.NoteDisplayInterface.prototype.body = null;
                  client.pageplugins.NoteDisplayInterface.prototype.head = null;
                  client.pageplugins.NoteDisplayInterface.prototype.server = null;
                  client.pageplugins.NoteDisplayInterface.prototype.document = null;
                  client.pageplugins.NoteDisplayInterface.prototype.window = null;
                  client.pageplugins.NoteDisplayInterface.prototype.frame = null;
                  client.pageplugins.NoteDisplayInterface.prototype.destroy = function() {
                     this.body = null;
                     this.document = null;
                     this.window = null;
                     this.frame = null;
                     this.head = null;
                     this.server = null;
                     this.triggerChange = null;
                     this.displayTool = null;
                     this.getTopics = null;
                     this.getCard = null;
                     this.openCard = null;
                     this.addMarker = null;
                     this.selectMarker = null;
                     this.getMarkers = null;
                     this.removeMarker = null;
                     this.getMarkerBar = null;
                     this.getMarkerBarOffset = null;
                     this.getCurrentUser = null;
                     this.addKeyHook = null;
                     this.removeKeyHook = null;
                     }
                  client.pageplugins.NoteDisplayInterface.prototype.displayTool = function(options) {
                     }
                  client.pageplugins.NoteDisplayInterface.prototype.getCard = function() {
                     }
                  client.pageplugins.NoteDisplayInterface.prototype.getTopics = function() {
                     }
                  client.pageplugins.NoteDisplayInterface.prototype.openCard = function(card) {
                     }
                  client.pageplugins.NoteDisplayInterface.prototype.addMarker = function(marker) {
                     }
                  client.pageplugins.NoteDisplayInterface.prototype.selectMarker = function(marker) {
                     }
                  client.pageplugins.NoteDisplayInterface.prototype.getMarkers = function() {
                     }
                  client.pageplugins.NoteDisplayInterface.prototype.removeMarker = function(marker) {
                     }
                  client.pageplugins.NoteDisplayInterface.prototype.getMarkerBar = function() {
                     }
                  client.pageplugins.NoteDisplayInterface.prototype.getMarkerBarOffset = function(element) {
                     }
                  client.pageplugins.NoteDisplayInterface.prototype.getCurrentUser = function() {
                     }
                  client.pageplugins.NoteDisplayInterface.prototype.triggerChange = function() {
                     }
                  client.pageplugins.NoteDisplayInterface.prototype.addKeyHook = function(handler) {
                     }
                  client.pageplugins.NoteDisplayInterface.prototype.removeKeyHook = function(handler) {
                     }
                  client.pageplugins.NoteDisplayPlugin = function() {
                     this.displayInterface = null;
                     }
                  client.pageplugins.NoteDisplayPlugin.prototype.displayInterface = null;
                  client.pageplugins.NoteDisplayPlugin.prototype.load = function() {
                     };
                  client.pageplugins.NoteDisplayPlugin.prototype.onChange = function() {
                     };
                  client.pageplugins.NoteDisplayPlugin.prototype.unload = function() {
                     };
                  client.pageplugins.NoteDisplayPlugin.prototype.onMainClick = function() {
                     };
                  client.pageplugins.NoteDisplayPlugin.prototype.loadMenuCreation = function(createItem) {
                     return;
                     };
                  client.pageplugins.displayplugins.Image = function() {
                     var me;
                     this.load = function() {
                        me = this;
                        if(this.displayInterface.isEditor) {
                           prepareElements();
                           }
                        }
                     this.onChange = function() {
                        if(this.displayInterface.isEditor) {
                           prepareElements();
                           }
                        }
                     /*this.loadMenuCreation=function(createItem){if(this.displayInterface.isEditor){createItem(lang.INSERT_IMAGE,"image-x-generic.png",insertImageHandler);}}
                     function insertImageHandler(event){showEditor(null);}*/
                     this.onMainClick = function() {
                        if(this.displayInterface.isEditor && editorShown) {
                           closeEditor();
                           }
                        }
                     this.unload = function() {
                        me = null;
                        }
                     function prepareElements(elm) {
                        if(elm === undefined) {
                           elm = $("img:not(.equation,.tex,.texrender)", me.displayInterface.body);
                           }
                        elm.bind("click", onImageClick).bind("contextmenu", onImageClick);
                        }
                     function onImageClick(event) {
                        event.stopPropagation();
                        showEditor($(this));
                        }
                     var editorShown;
                     var paddingBottomField, paddingLeftField, paddingRightField, paddingTopField;
                     function showEditor(elm) {
                        var content = $(document.createElement("div"));
                        var panel = utility.createPanelDialog(content, {
                           }
                        , undefined, lang.IMAGE_EDITOR);
                        var createNew = (elm == null);
                        var urlField;
                        var frameId = "upload" + (new Date().getTime());
                        var urlField, changeUrlButton, form, loader, widthField, heightField, closeButton, deleteButton;
                        $(document.createElement("h3")).text(lang.IMAGE_UPLOAD_HEADING).appendTo(content);
                        $("<iframe name=\"" + frameId + "\"></iframe>").css("display", "none").attr( {
                           id : frameId}
                        ).appendTo(content);
                        form = $("<form name=\"imageform\" method=\"post\" enctype=\"multipart/form-data\"></form>").attr( {
                           action : config.uploadPath, target : frameId}
                        ).appendTo(content);
                        var toolset;
                        toolset = utility.createToolbar().appendTo(form);
                        $(document.createElement("span")).text(lang.PICTURE).appendTo(toolset);
                        $("<input type=\"hidden\" name=\"action\" value=\"imageUpload\">").appendTo(form);
                        var card = me.displayInterface.getCard();
                        var topicId = card.step.topic.id;
                        $("<input type=\"hidden\" name=\"topic\" value=\"" + topicId + "\">").appendTo(form);
                        if(config.customArgs) {
                           for(var i in config.customArgs) {
                              form.append($("<input type=\"hidden\" name=\"" + i + "\" value=\"" + config.customArgs[i] + "\">"));
                              }
                           }
                        $('<input accept="image/gif,image/jpeg,image/pjpeg,image/png,image/x-bmp" type="file" class="file auto" name="file">').attr("size", "10").appendTo(toolset);
                        loader = utility.createMenuItem("go-up.png", null, lang.UPLOAD_BUTTON, null).appendTo(toolset);
                        $(document.createElement("h3")).text(lang.IMAGE_LOCATION_HEADING).appendTo(content);
                        var editToolbar = utility.createToolbar().appendTo(content);
                        $(document.createElement("span")).text(lang.LOCATION).appendTo(editToolbar);
                        urlField = $(document.createElement("input")).attr("type", "text").addClass("large").attr( {
                           size : "20"}
                        ).val(elm ? elm.attr("src") : "").appendTo(editToolbar);
                        if(createNew) {
                           changeUrlButton = utility.createMenuItem("dialog-apply.png", null, lang.INSERT, null).appendTo(editToolbar);
                           }
                        if(!createNew) {
                           var advancedLabel = $(document.createElement("h3")).text(lang.IMAGE_ADJUST_HEADING).makeCollapsible();
                           content.append(advancedLabel);
                           var advancedContent = $(document.createElement("div")).appendTo(content).hide();
                           var sizeFloatBar = utility.createToolbar().appendTo(advancedContent);
                           $(document.createElement("span")).text(lang.WIDTH).appendTo(sizeFloatBar);
                           widthField = $(document.createElement("input")).attr("type", "text").attr("size", "10").appendTo(sizeFloatBar);
                           $(document.createElement("label")).text(lang.HEIGHT).appendTo(sizeFloatBar);
                           heightField = $(document.createElement("input")).attr("type", "text").attr("size", "10").appendTo(sizeFloatBar);
                           $(document.createElement("span")).text(lang.FLOAT_STYLE).appendTo(sizeFloatBar);
                           floatSelector = $(document.createElement("select")).appendTo(sizeFloatBar);
                           $(document.createElement("option")).text(lang.FLOAT_LEFT).val("left").appendTo(floatSelector);
                           $(document.createElement("option")).text(lang.FLOAT_RIGHT).val("right").appendTo(floatSelector);
                           $(document.createElement("option")).text(lang.FLOAT_NONE).val("none").appendTo(floatSelector);
                           var padAttr = {
                              maxlength : "5", size : "3", type : "text"};
                           var paddingLeftBar = utility.createToolbar().appendTo(advancedContent);
                           $(document.createElement("span")).text(lang.PADDING_STYLE).appendTo(paddingLeftBar);
                           $(document.createElement("label")).text(lang.PADDING_LEFT).appendTo(paddingLeftBar);
                           paddingLeftField = $(document.createElement("input")).attr(padAttr).appendTo(paddingLeftBar);
                           $(document.createElement("label")).text(lang.PADDING_RIGHT).appendTo(paddingLeftBar);
                           paddingRightField = $(document.createElement("input")).attr(padAttr).appendTo(paddingLeftBar);
                           var paddingTopBar = utility.createToolbar().appendTo(advancedContent);
                           $(document.createElement("span")).text(lang.PADDING_STYLE).appendTo(paddingTopBar);
                           $(document.createElement("label")).text(lang.PADDING_TOP).appendTo(paddingTopBar);
                           paddingTopField = $(document.createElement("input")).attr(padAttr).appendTo(paddingTopBar);
                           $(document.createElement("label")).text(lang.PADDING_BOTTOM).appendTo(paddingTopBar);
                           paddingBottomField = $(document.createElement("input")).attr(padAttr).appendTo(paddingTopBar);
                           advancedLabel.triggerHandler("click");
                           }
                        if(!createNew) {
                           panel.toolbar.append(closeButton = utility.createMenuItem("dialog-apply.png", null, createNew ? lang.INSERT : lang.CLOSE).addClass("right").attr("accesskey", "o"))}
                        panel.toolbar.append(deleteButton = utility.createMenuItem(createNew ? "process-stop.png" : "edit-delete.png", null, createNew ? lang.CANCEL : lang.DELETE).addClass("right").attr("accesskey", "n"));
                        if(createNew) {
                           changeUrlButton.bind("click", {
                              box : urlField}
                           , function(e) {
                              var url = e.data.box.val(); if(url.length == 0) {
                                 alert(lang.NO_URL_GIVEN); return; }
                              try {
                                 createNewImage(url); }
                              catch(ex) {
                                 alert(lang.URL_INVALID + ex); }
                              }
                           );
                           }
                        else {
                           closeButton.bind("click", form, closeEditor);
                           urlField.bind("blur", {
                              img : elm, box : urlField}
                           , function(e) {
                              try {
                                 e.data.img.attr("src", e.data.box.val()); }
                              catch(ex) {
                                 alert(lang.URL_INVALID + ex); }
                              }
                           );
                           heightField.val(elm.css("height")).bind("blur", {
                              elm : elm, property : "height"}
                           , valueHandler);
                           widthField.val(elm.css("width")).bind("blur", {
                              elm : elm, property : "width"}
                           , valueHandler);
                           paddingLeftField.bind("blur", {
                              elm : elm, property : "padding-left"}
                           , valueHandler);
                           paddingRightField.bind("blur", {
                              elm : elm, property : "padding-right"}
                           , valueHandler);
                           paddingTopField.bind("blur", {
                              elm : elm, property : "padding-top"}
                           , valueHandler);
                           paddingBottomField.bind("blur", {
                              elm : elm, property : "padding-bottom"}
                           , valueHandler);
                           switch(elm.css("float").toLowerCase()) {
                              case"left" : floatSelector.val("left");
                              break;
                              case"right" : floatSelector.val("right");
                              break;
                              default : floatSelector.val("none");
                              break;
                              }
                           floatSelector.bind("change", elm, function(event) {
                              var style = $(this).val(); var curStyle = event.data.css("float"); event.data.css("float", style); if(curStyle !== style) {
                                 event.data.css( {
                                    "padding-right" : style == "left" ? 10 : 0, "padding-left" : style == "right" ? 10 : 0, "padding-bottom" : style == "none" ? 0 : 10}
                                 ); updateEditor(); }
                              me.displayInterface.triggerChange(); }
                           );
                           }
                        deleteButton.bind("click", elm, createNew ? closeEditor : function(event) {
                           var img = event.data; closeEditor(); img.remove(); me.displayInterface.triggerChange(); }
                        );
                        loader.bind("click", form, function(event) {
                           event.data.submit(); }
                        );
                        lastElm = createNew ? null : elm;
                        if(!createNew) {
                           updateEditor();
                           }
                        utility.globalCallback = createNew ? createNewCallback : replaceCallback;
                        me.displayInterface.displayTool( {
                           content : panel, handler : editorClosed, offset : createNew ? 150 : 50}
                        );
                        editorShown = true;
                        }
                     function updateEditor() {
                        if(lastElm) {
                           paddingLeftField.val(lastElm.css("padding-left"));
                           paddingRightField.val(lastElm.css("padding-right"));
                           paddingTopField.val(lastElm.css("padding-top"));
                           paddingBottomField.val(lastElm.css("padding-bottom"));
                           }
                        }
                     function valueHandler(e) {
                        var elm = e.data.elm;
                        var property = e.data.property;
                        var name = e.data.name;
                        try {
                           var val = $(this).val();
                           if(utility.isInteger(val)) {
                              val = val + "px";
                              }
                           elm.css(property, val);
                           me.displayInterface.triggerChange();
                           }
                        catch(ex) {
                           alert(lang.INVALID_START + name + lang.INVALID_END);
                           }
                        }
                     var lastElm;
                     function replaceCallback(name, error) {
                        if(name) {
                           replace(name);
                           closeEditor();
                           }
                        else {
                           alert(getMessageForError(error));
                           }
                        }
                     function replace(url) {
                        me.displayInterface.triggerChange();
                        lastElm.attr("src", url);
                        }
                     function createNewCallback(url, error) {
                        if(url) {
                           createNewImage(url);
                           }
                        else {
                           alert(getMessageForError(error));
                           }
                        }
                     function getMessageForError(error) {
                        switch(error) {
                           case"moveerror" : return lang.ERROR_MOVE;
                           case"illegaltype" : return lang.ERROR_TYPE;
                           case"notenoughquota" : return lang.ERROR_QUOTA;
                           case"toobig" : return lang.ERROR_TOO_BIG;
                           case"nofileuploaded" : return lang.ERROR_NO_FILE;
                           case"other" : return lang.ERROR_WHILE_UPLOADING;
                           default : return lang.ERROR_WHILE_UPLOADING;
                           }
                        }
                     function createNewImage(url) {
                        var img = $(me.displayInterface.document.createElement("img")).attr( {
                           border : "0", src : url}
                        ).css( {
                           "float" : "right", "padding" : "0 0 10px 10px"}
                        ).insertNearSelection(true);
                        prepareElements(img);
                        closeEditor();
                        me.displayInterface.triggerChange();
                        img.selectNodes( {
                           collapse : false}
                        );
                        }
                     function closeEditor() {
                        if(editorShown) {
                           me.displayInterface.displayTool();
                           }
                        }
                     function editorClosed() {
                        editorShown = false;
                        if(lastElm) {
                           lastElm.selectNodes();
                           lastElm = null;
                           }
                        delete utility.globalCallback;
                        }
                     }
                  client.pageplugins.displayplugins.Image.prototype = new client.pageplugins.NoteDisplayPlugin;
                  client.pageplugins.displayplugins.Link = function() {
                     var me;
                     this.load = function() {
                        me = this;
                        prepareElements($("a", this.displayInterface.body));
                        }
                     this.loadMenuCreation = function(createItem) {
                        if(this.displayInterface.isEditor) {
                           createItem(lang.INSERT_LINK, "link.png", insertLinkHandler).attr("accesskey", "l");
                           }
                        }
                     var dummyNode;
                     function insertLinkHandler(event) {
                        if(!$.hasSelection(me.displayInterface.document)) {
                           alert(lang.LINK_REQUIRES_SELECTION);
                           return;
                           }
                        dummyNode = $(me.displayInterface.document.createElement("a")).attr("href", "").takeSelection();
                        prepareElements(dummyNode);
                        showEditor(dummyNode);
                        }
                     this.unload = function() {
                        me = null;
                        }
                     var editorShown;
                     function closeEditor() {
                        if(editorShown) {
                           me.displayInterface.displayTool();
                           }
                        if(currentElement) {
                           hideSelectionMap();
                           typeSelector = txtCardTitle = currentElement = null;
                           currentElement = null;
                           }
                        }
                     this.onChange = function() {
                        if(this.displayInterface.isEditor) {
                           prepareElements($("a", this.displayInterface.body));
                           }
                        }
                     function prepareElements(elements) {
                        var handler = me.displayInterface.isEditor ? editLinkHandler : studyLinkHandler;
                        elements.bind("click", handler);
                        if(me.displayInterface.isEditor) {
                           elements.bind("contextmenu", handler);
                           }
                        }
                     function studyLinkHandler(event) {
                        event.stopPropagation();
                        event.preventDefault();
                        var link = $(this);
                        var id = link.attr("href");
                        if(link.hasClass("cardlink")) {
                           var li =- 1;
                           if((li = id.lastIndexOf("#")) >- 1) {
                              id = id.substr(li);
                              }
                           if(id.substr(0, 1) == "#") {
                              id = id.substr(1);
                              var card = findCard(parseInt(id));
                              if(card) {
                                 me.displayInterface.openCard(card);
                                 }
                              }
                           }
                        else {
                           window.open(id, "MediabirdExternal");
                           }
                        }
                     function findCard(id) {
                        var card;
                        var topics = me.displayInterface.getTopics();
                        for(var i = 0; i < topics.length; i++) {
                           var topic = topics[i];
                           card = topic.getCardById(id);
                           if(card)break;
                           }
                        return card;
                        }
                     var currentElement;
                     var txtCardTitle;
                     var typeSelector;
                     var currentMap;
                     function editLinkHandler(event) {
                        event.stopPropagation();
                        event.preventDefault();
                        if(!editorShown) {
                           showOverlay(this);
                           }
                        }
                     function showOverlay(link) {
                        var pos = utility.getAbsolutePosition(link);
                        pos.top += link.offsetHeight;
                        pos.height = 28;
                        pos.width = 300;
                        if(pos.left + pos.width > client.pageplugins.NoteDisplay.DISPLAY_WIDTH) {
                           pos.left = client.pageplugins.NoteDisplay.DISPLAY_WIDTH - pos.width;
                           }
                        if(pos.top + pos.height > client.pageplugins.NoteDisplay.DISPLAY_HEIGHT) {
                           pos.top = client.pageplugins.NoteDisplay.DISPLAY_HEIGHT - pos.height;
                           }
                        var panel = $(document.createElement("div"));
                        var toolbar = utility.createToolbar().addClass("noclear").appendTo(panel);
                        var followLink = utility.createMenuItem("link.png", null, lang.FOLLOW_LINK).appendTo(toolbar).bind("click", link, function(event) {
                           event.preventDefault(); closeEditor(); studyLinkHandler.call(event.data, event); }
                        );
                        var href = $(link).attr("href");
                        if(getLinkType($(link)) != "card") {
                           followLink.attr( {
                              "href" : href, "title" : lang.FOLLOW_LINK + " " + href}
                           );
                           }
                        else {
                           var card = findCard(parseInt(href.substr(1)));
                           if(card) {
                              followLink.attr( {
                                 "title" : lang.OPEN_SHEET + " " + card.title}
                              );
                              }
                           }
                        utility.createMenuItem("edit.png", null, lang.EDIT_LINK).appendTo(toolbar).bind("click", link, function(event) {
                           showEditor($(event.data)); }
                        );
                        utility.createMenuItem("edit-delete.png", null, lang.REMOVE).appendTo(toolbar).bind("click", link, removeLinkHandler);
                        me.displayInterface.displayTool( {
                           content : panel, coords : pos, clickCloses : true, closer : true, noFocus : true, handler : function() {
                              editorShown = false; }
                           }
                        );
                        editorShown = true;
                        }
                     function getLinkType(element) {
                        var href = element.attr("href");
                        if(href === undefined) {
                           return"url";
                           }
                        return element.hasClass("cardlink") ? "card" : (href.toLowerCase().substr(0, 7) == "mailto:" ? "email" : "url")}
                     function showEditor(element) {
                        currentElement = element;
                        var selectCardButton, topicSelector, linkBox, removeButton, closeButton;
                        var href = element.attr("href") || "";
                        var type = getLinkType(element);
                        if(type == "email") {
                           href = href.substr(7);
                           }
                        else if(type == "card") {
                           var li =- 1;
                           if((li = href.lastIndexOf("#")) >- 1) {
                              href = href.substr(li);
                              }
                           }
                        var content = $(document.createElement("div"));
                        var panel = utility.createPanelDialog(content, undefined, undefined, lang.LINK_HEADING);
                        typeSelector = $(document.createElement("select")).attr("name", "type").addClass("auto").append($(document.createElement("option")).val("url").text(lang.TYPE_URL)).append($(document.createElement("option")).val("card").text(lang.TYPE_CARD)).append($(document.createElement("option")).val("email").text(lang.TYPE_EMAIL));
                        var row1 = utility.createToolbar().append($(document.createElement("span")).text(lang.LINK_TYPE)).append(typeSelector);
                        urlRow = utility.createToolbar().append($(document.createElement("span")).text(lang.LOCATION)).append(linkBox = $(document.createElement("input")).attr("type", "text").addClass("large").val(href));
                        urlRow.css("display", type != "card" ? "" : "none");
                        topicSelector = $(document.createElement("select")).addClass("large");
                        topicRow = utility.createToolbar().append($(document.createElement("span")).text(lang.TOPIC)).append(topicSelector);
                        selectCardButton = utility.createMenuItem("map-view.png", null, lang.SELECT_CARD, null).css( {
                           "float" : "right"}
                        );
                        cardRow = utility.createToolbar().append(selectCardButton).append($(document.createElement("span")).text(lang.NOTE_SHEET)).append(txtCardTitle = $(document.createElement("span")));
                        txtCardTitle.css( {
                           "white-space" : "nowrap", "font-weight" : "bold", "overflow" : "hidden"}
                        );
                        panel.toolbar.append(closeButton = utility.createMenuItem("dialog-apply.png", null, lang.CLOSE, null).addClass("right")).append(removeButton = utility.createMenuItem("edit-delete.png", null, lang.REMOVE_LINK, null).addClass("right"));
                        removeButton.bind("click", element.get(0), removeLinkHandler);
                        closeButton.bind("click", element, function(event) {
                           closeEditor(); }
                        )topicRow.css("display", type == "card" ? "" : "none");
                        topicSelector.append($(document.createElement("option")).text("").val("")).css("max-width", "300px");
                        var topics = me.displayInterface.getTopics();
                        topics.sort(function(a, b) {
                           var sortA = a.title; var sortB = b.title; if(sortA == sortB) {
                              return 0; }
                           return(sortA.toUpperCase() > sortB.toUpperCase()) ? 1 :- 1; }
                        );
                        var cardInEditor = me.displayInterface.getCard();
                        var curTopicIndex;
                        var topicIndex =- 1;
                        var card;
                        for(var i = 0; i < topics.length; i++) {
                           var topic = topics[i];
                           var _card;
                           if(type == "card" && href.length > 1 && href.substr(0, 1) == "#" && (_card = topic.getCardById(parseInt(href.substr(1)))) != null) {
                              topicIndex = i;
                              card = _card;
                              }
                           if(topic === cardInEditor.step.topic) {
                              curTopicIndex = i;
                              }
                           topicSelector.append($(document.createElement("option")).text(topic.title).val("" + i));
                           }
                        typeSelector.val(type);
                        typeSelector.bind("change", {
                           url : urlRow, topicRow : topicRow, topicSelector : topicSelector, cardRow : cardRow}
                        , function(event) {
                           var isCard = $(this).val() == "card"; event.data.url.css("display", !isCard ? "" : "none"); event.data.topicRow.css("display", isCard ? "" : "none"); event.data.cardRow.css("display", (isCard && event.data.topicSelector.val() != "") ? "" : "none"); }
                        );
                        topicSelector.bind("change", cardRow, function(event) {
                           var show = $(this).val() != ""; event.data.css("display", show ? "" : "none"); }
                        ).val((topicIndex >- 1 ? topicIndex : curTopicIndex).toString());
                        cardRow.css("display", topicIndex >- 1 ? "" : "none");
                        txtCardTitle.text(topicIndex >- 1 ? card.title : lang.NO_CARD_TITLE);
                        var selectArgs = {
                           topics : topics, selector : topicSelector, panel : content, element : element, row : cardRow.css("position", "relative")};
                        selectCardButton.bind("click", selectArgs, selectCardButtonHandler);
                        content.append(row1).append(urlRow).append(topicRow).append(cardRow);
                        linkBox.bind("blur", {
                           element : element, box : linkBox}
                        , linkEditorHandler);
                        me.displayInterface.displayTool( {
                           content : panel, handler : function() {
                              editorShown = false; }
                           , offset : 160, clickCloses : true}
                        );
                        editorShown = true;
                        }
                     function removeLinkHandler(event) {
                        var element = $(event.data);
                        element.contents().insertBefore(element);
                        element.remove();
                        closeEditor();
                        me.displayInterface.triggerChange();
                        }
                     function linkEditorHandler(event) {
                        var box = event.data.box;
                        var element = event.data.element;
                        var href = box.val();
                        if(typeSelector.val() == "email") {
                           href = "mailto:" + href;
                           }
                        element.attr("href", href);
                        element.removeClass("cardlink");
                        element.selectNodes( {
                           collapse : false}
                        );
                        me.displayInterface.triggerChange();
                        }
                     function selectCardButtonHandler(event) {
                        event.stopPropagation();
                        var topics = event.data.topics;
                        var selector = event.data.selector;
                        var panel = event.data.panel;
                        var element = event.data.element;
                        if(selector.val() == "") {
                           return;
                           }
                        var pos = {
                           };
                        pos.right = 140;
                        pos.top =- 70;
                        pos.width = 350;
                        var topic = topics[parseInt(selector.val())];
                        var card = null;
                        var href = element.attr("href");
                        if(href && href.length > 1) {
                           card = topic.getCardById(parseInt(href.substr(1)));
                           }
                        currentMap = client.widgets.MapView.createMapView(topic, false, event.data.row, pos, null, {
                           onCreateMenu : createMapMenu}
                        , lang.SELECT_CARD_EXPLANATION, card);
                        currentMap.toolbar.remove();
                        $("div.mapView", currentMap.container).css("max-height", 250);
                        currentMap.container.css("position", "absolute");
                        panel.bind("click", hideSelectionMap);
                        }
                     function createMapMenu(fargs) {
                        var card = fargs.card;
                        var menu = fargs.menu;
                        utility.createMenuItem("link.png", "selectCard", lang.SELECT_CARD, function(event) {
                           event.stopPropagation(); var card = event.data.card; txtCardTitle.text(card.title); currentElement.attr("href", "#" + card.id); currentElement.addClass("cardlink"); currentElement.selectNodes( {
                              collapse : false}
                           ); hideSelectionMap(); }
                        , fargs).appendTo(menu);
                        me.displayInterface.triggerChange();
                        }
                     function hideSelectionMap() {
                        if(currentMap) {
                           client.widgets.MapView.destroyMap(currentMap);
                           currentMap = null;
                           }
                        }
                     }
                  client.pageplugins.displayplugins.Link.prototype = new client.pageplugins.NoteDisplayPlugin;
                  client.pageplugins.displayplugins.Table = function() {
                     var me;
                     this.load = function() {
                        me = this;
                        if(this.displayInterface.isEditor) {
                           prepareElements();
                           this.displayInterface.addKeyHook(bodyKeyHandler);
                           }
                        }
                     this.onChange = function() {
                        if(currentTable !== undefined) {
                           updateOverlay();
                           }
                        }
                     this.onMainClick = function() {
                        if(this.displayInterface.isEditor) {
                           prepareElements(undefined, currentTable !== undefined);
                           }
                        }
                     this.loadMenuCreation = function(createItem) {
                        if(this.displayInterface.isEditor) {
                           createItem(lang.INSERT_TABLE, "insert-table.png", insertTableHandler).attr("accesskey", "t");
                           }
                        }
                     function insertTableHandler(event) {
                        var table = $("<table><tr><th><br/></th></tr><tr><td><br/></td></tr></table>", me.displayInterface.document);
                        table.insertNearSelection(true);
                        me.displayInterface.triggerChange();
                        showOverlay(table.eq(0));
                        $("th:first", table).selectNodes();
                        }
                     this.unload = function() {
                        me = null;
                        if(this.displayInterface.isEditor) {
                           this.displayInterface.removeKeyHook(bodyKeyHandler);
                           }
                        }
                     function prepareElements(elm, checkBlock) {
                        if(elm === undefined) {
                           elm = $("table", me.displayInterface.body);
                           }
                        elm.bind("click", onTableClick).bind("contextmenu", onTableClick);
                        for(var i = 0; i < elm.length; i++) {
                           var table = elm.eq(i);
                           var rows = $("tr", table);
                           var first =!elm.hasClass("no-header");
                           rows.each(function() {
                              var row = $(this); row.children(first ? "td" : "th").each(function() {
                                 var cell = $(this); var colspan = cell.attr("colspan"); var replacement = $(me.displayInterface.document.createElement(first ? "th" : "td")).append(cell.contents()); cell.replaceWith(replacement); if(colspan !== undefined) {
                                    replacement.attr("colspan", colspan); }
                                 }
                              ); first = false; var children = row.children("td,th"); children.not(":last").addClass("r"); children.filter(":last").removeClass("r"); }
                           );
                           table.removeAttr("style");
                           if(checkBlock) {
                              var blockNode = "p,h1,h2,h3,h4,h5,h6,pre,br,ul,ol,table";
                              if(!table.prev().is(blockNode)) {
                                 insertBlock(table, true);
                                 }
                              if(!table.next().is(blockNode)) {
                                 insertBlock(table, false);
                                 }
                              }
                           }
                        function insertBlock(table, before) {
                           var br = $(me.displayInterface.document.createElement("br"));
                           var p = $(me.displayInterface.document.createElement("p")).append(br);
                           $.fn[before ? "insertBefore" : "insertAfter"].call(p, table);
                           br.selectNodes();
                           }
                        }
                     function onTableClick(event) {
                        event.stopPropagation();
                        var me = $(this);
                        showOverlay(me);
                        }
                     var currentTable;
                     function showOverlay(table) {
                        if(currentTable !== undefined && table.index(currentTable) == 0) {
                           return;
                           }
                        currentTable = table;
                        var pos = updateOverlay();
                        var panel = $(document.createElement("div"));
                        var toolbar = utility.createToolbar().addClass("noclear").appendTo(panel);
                        utility.createButton("insert-columns.png", "addcol", lang.INSERT_COLUMN, modifyTableHandler).appendTo(toolbar);
                        utility.createButton("insert-rows.png", "addrow", lang.INSERT_ROW, modifyTableHandler).appendTo(toolbar);
                        utility.createButton("delete-columns.png", "delcol", lang.DELETE_COLUMN, modifyTableHandler).appendTo(toolbar);
                        utility.createButton("delete-rows.png", "delrow", lang.DELETE_ROW, modifyTableHandler).appendTo(toolbar);
                        utility.createMenuItem("edit-delete.png", null, lang.DELETE).attr("accesskey", "r").appendTo(toolbar).bind("click", table, removeTableHandler);
                        currentTable.toolWindow = me.displayInterface.displayTool( {
                           content : panel, coords : pos, closer : true, clickCloses : true, noFocus : true, handler : function() {
                              currentTable = undefined; }
                           }
                        );
                        }
                     function bodyKeyHandler(event) {
                        var noModifiers =!event.ctrlKey &&!event.altKey &&!event.metaKey;
                        var tabPressed = event.which == 9 && noModifiers;
                        if(tabPressed) {
                           var sel = $.getSelection( {
                              context : me.displayInterface.document, disableSurrounding : true}
                           );
                           if(sel.length > 0) {
                              sel = sel.closest("td,th");
                              }
                           if(sel.length > 0) {
                              var table = sel.closest("table");
                              var cells = $("th,td", table);
                              var index = cells.index(sel);
                              if(tabPressed) {
                                 event.preventDefault();
                                 index += (event.shiftKey ?- 1 : 1);
                                 if(index < cells.length && index >= 0) {
                                    cells.eq(index).selectNodes();
                                    }
                                 else if(!event.shiftKey) {
                                    var newRow = $(me.displayInterface.document.createElement("tr")).html($("tr:last", table).html());
                                    newRow.children().empty();
                                    table.append(newRow);
                                    $("td,th", newRow).eq(0).append(me.displayInterface.document.createElement("br")).selectNodes();
                                    }
                                 else {
                                    table.prev().selectNodes( {
                                       collapse : false}
                                    );
                                    }
                                 }
                              return false;
                              }
                           }
                        }
                     function updateOverlay() {
                        var pos = utility.getAbsolutePosition(currentTable.get(0));
                        pos.top += currentTable.height();
                        pos.height = 28;
                        pos.width = 220;
                        if(pos.left + pos.width > client.pageplugins.NoteDisplay.DISPLAY_WIDTH) {
                           pos.left = client.pageplugins.NoteDisplay.DISPLAY_WIDTH - pos.width;
                           }
                        if(pos.top + pos.height > client.pageplugins.NoteDisplay.DISPLAY_HEIGHT) {
                           pos.top = client.pageplugins.NoteDisplay.DISPLAY_HEIGHT - pos.height;
                           }
                        if(currentTable.toolWindow !== undefined) {
                           currentTable.toolWindow.css(pos);
                           }
                        else {
                           return pos;
                           }
                        }
                     function modifyTableHandler(event) {
                        event.stopPropagation();
                        var cell = $.getSelection( {
                           context : me.displayInterface.document, disableSurrounding : true}
                        ).filter(":last");
                        var rows = $("tr", currentTable);
                        var chain = cell.parents("table");
                        if(chain.index(currentTable) == 0) {
                           cell = cell.closest("td,th");
                           }
                        else {
                           cell = rows.filter(":last").children("td,th").filter(":last");
                           }
                        var row = cell.closest("tr");
                        var rowNum = rows.index(row);
                        var colNum = row.children("td,th").index(cell);
                        var isAdd = event.data.action.search("add") == 0;
                        var isEmpty = false;
                        switch(event.data.action) {
                           case"addrow" : var newRow = $(me.displayInterface.document.createElement("tr")).html(row.html());
                           newRow.children().empty();
                           newRow.insertAfter(row);
                           newRow.children(":first").html("&nbsp;").selectNodes();
                           me.displayInterface.triggerChange();
                           break;
                           case"delrow" : row.next().children("td,th").filter(":first").selectNodes();
                           row.remove();
                           me.displayInterface.triggerChange();
                           break;
                           case"addcol" : case"delcol" : rows.each(function() {
                              var cols = $("td,th", this); var pcols = 0; cols.each(function() {
                                 var elem = $(this); var colSpan = parseInt(elem.attr("colspan")); if(isNaN(colSpan) || colSpan == 0) {
                                    colSpan = 1; }
                                 pcols += colSpan; if(pcols > colNum) {
                                    if(isAdd) {
                                       elem.after($(me.displayInterface.document.createElement("td"))); }
                                    else {
                                       colSpan--; if(colSpan == 0) {
                                          elem.next().selectNodes(); elem.remove(); }
                                       else {
                                          elem.attr("colspan", colSpan); }
                                       }
                                    return false; }
                                 }
                              ); }
                           );
                           me.displayInterface.triggerChange();
                           break;
                           }
                        if(!isAdd) {
                           var cells = $("td,th", currentTable);
                           if(cells.length == 0) {
                              currentTable.remove();
                              closeEditor();
                              return;
                              }
                           }
                        updateOverlay();
                        prepareElements(currentTable);
                        }
                     function removeTableHandler(event) {
                        event.data.remove();
                        closeEditor();
                        me.displayInterface.triggerChange();
                        }
                     function closeEditor() {
                        me.displayInterface.displayTool();
                        }
                     }
                  client.pageplugins.displayplugins.Table.prototype = new client.pageplugins.NoteDisplayPlugin;
                  client.pageplugins.displayplugins.HTML = function() {
                     var me;
                     this.load = function() {
                        me = this;
                        }
                     this.loadMenuCreation = function(createItem) {
                        if(this.displayInterface.isEditor) {
                           createItem(lang.INSERT_HTML, "html.png", insertHTMLHandler);
                           createItem(lang.INSERT_CODE, "code.png", insertCodeHandler);
                           }
                        }
                     var dummyNode;
                     function insertHTMLHandler(event) {
                        insertCode(true);
                        }
                     function insertCodeHandler(event) {
                        insertCode(false);
                        }
                     function insertCode(insertHTML) {
                        dummyNode = $(me.displayInterface.document.createElement("span")).takeSelection();
                        var textField;
                        var cancelButton, okButton;
                        textField = $(document.createElement("textarea")).addClass("code").css( {
                           "width" : "100%", "height" : 200}
                        ).text(insertHTML ? dummyNode.html() : dummyNode.text());
                        textField.insertHTML = insertHTML;
                        var panel = utility.createPanelDialog(textField, {
                           }
                        , undefined, insertHTML ? lang.HTML_CODE : lang.CODE)panel.toolbar.append(okButton = utility.createMenuItem("dialog-apply.png", null, lang.INSERT, null).addClass("right").attr("accesskey", "o")).append(cancelButton = utility.createMenuItem("process-stop.png", null, lang.CANCEL, null).addClass("right").attr("accesskey", "n"));
                        cancelButton.bind("click", function() {
                           dummyNode = null; me.displayInterface.displayTool(); }
                        );
                        okButton.bind("click", textField, insertClickHandler);
                        me.displayInterface.displayTool( {
                           content : panel, handler : undefined, offset : 150}
                        );
                        }
                     function insertClickHandler(event) {
                        var textField = event.data;
                        var code = textField.val();
                        dummyNode.replaceWith(textField.insertHTML ? $(me.displayInterface.document.createElement("div")).html(code).contents() : $(me.displayInterface.document.createElement("pre")).text(code));
                        dummyNode = null;
                        me.displayInterface.triggerChange();
                        me.displayInterface.displayTool();
                        }
                     this.unload = function() {
                        me = null;
                        }
                     }
                  client.pageplugins.displayplugins.HTML.prototype = new client.pageplugins.NoteDisplayPlugin;
                  client.pageplugins.displayplugins.LaTeXmage = function() {
                     var latexChecked = false;
                     var latexFound;
                     var me;
                     this.load = function() {
                        me = this;
                        if(!latexChecked) {
                           latexFound = false;
                           me.displayInterface.server.checkEquationSupport(utility.createCallback(null, checkSuccess));
                           latexChecked = true;
                           }
                        if(latexFound) {
                           prepareElements();
                           }
                        }
                     function checkSuccess(data) {
                        latexFound = data.exists == true;
                        if(latexFound) {
                           prepareElements();
                           }
                        }
                     this.loadMenuCreation = function(createItem) {
                        if(this.displayInterface.isEditor && latexFound) {
                           createItem(lang.INSERT_LATEX, "equation.png", insertLaTeXHandler).attr("accesskey", "x");
                           }
                        }
                     function insertLaTeXHandler(event) {
                        var initialText = $.getSelectionText(me.displayInterface.document);
                        if(initialText === undefined || initialText == "") {
                           initialText = "$ $";
                           }
                        var checkText = initialText.replace("\\$", "");
                        if(initialText.indexOf("$") ==- 1 && initialText.indexOf("\\begin{") ==- 1 && initialText.indexOf("\\[") ==- 1 && initialText.indexOf("\\(") ==- 1) {
                           initialText = "$ " + initialText + " $";
                           }
                        var node = $(me.displayInterface.document.createElement("span")).takeSelection();
                        var createNew = true;
                        showInsertDialog(node, initialText, createNew);
                        }
                     this.onChange = function() {
                        prepareElements();
                        }
                     function prepareElements() {
                        if(me.displayInterface.isEditor) {
                           var equations = ($("img.equation,img.texrender,img.tex", me.displayInterface.body));
                           equations.filter("img.tex").each(function() {
                              var alt = $(this).attr("alt"); alt = "$" + alt + "$"; $(this).attr("alt", alt); }
                           );
                           equations.removeClass("tex texrender").addClass("equation").bind("click", equationClickHandler).bind("contextmenu", equationClickHandler);
                           }
                        }
                     this.onMainClick = function() {
                        if(this.displayInterface.isEditor && editorShown) {
                           closeEditor();
                           }
                        }
                     function equationClickHandler(event) {
                        event.stopPropagation();
                        var node = $(this);
                        var createNew = false;
                        showInsertDialog(node, node.attr("alt"), createNew);
                        }
                     var editorShown;
                     function showInsertDialog(node, initialText, createNew) {
                        var textField;
                        var cancelButton, okButton;
                        textField = $(document.createElement("textarea")).addClass("code").css( {
                           "width" : "100%", "height" : 180}
                        ).val(initialText);
                        var panel = utility.createPanelDialog(textField, {
                           }
                        , undefined, lang.LATEX_EDITOR);
                        var helpButton = utility.createMenuItem("dialog-question.png", null, lang.HELP).attr( {
                           "href" : lang.LATEX_HELP_LINK, "target" : "_blank"}
                        );
                        panel.toolbar.append(helpButton);
                        if(createNew) {
                           var convertButton = utility.createMenuItem("equation.png", null, lang.AUTO_CONVERT_LATEX, convertLaTeXHandler).addClass("right").attr("title", lang.AUTO_CONVERT_EXPLANATION).css("float", "left");
                           panel.toolbar.append(convertButton);
                           }
                        panel.toolbar.append(okButton = utility.createMenuItem("dialog-apply.png", null, createNew ? lang.INSERT : lang.UPDATE, null).addClass("right").attr("accesskey", "o")).append(cancelButton = utility.createMenuItem("process-stop.png", null, lang.CANCEL, null).addClass("right").attr("accesskey", "n"));
                        if(!createNew) {
                           var optionToolbar = utility.createToolbar().insertBefore(panel.toolbar);
                           var resetButton = utility.createMenuItem("edit-undo.png", null, lang.RESET_SIZE, null).appendTo(optionToolbar);
                           resetButton.bind("click", node, function(event) {
                              event.data.removeAttr("style").removeAttr("width").removeAttr("height"); me.displayInterface.triggerChange(); }
                           );
                           var blockCheck = $(document.createElement("input")).attr( {
                              "type" : "checkbox", "id" : "latexBlockCheck"}
                           ).appendTo(optionToolbar);
                           var blockLabel = $(document.createElement("label")).attr( {
                              "for" : "latexBlockCheck"}
                           ).text(lang.BLOCK_MODE).appendTo(optionToolbar);
                           blockCheck.get(0).checked = node.hasClass("block");
                           blockCheck.bind("change", node, function(event) {
                              jQuery.fn[this.checked ? "addClass" : "removeClass"].call(event.data, "block"); me.displayInterface.triggerChange(); }
                           );
                           }
                        cancelButton.bind("click", function() {
                           me.displayInterface.displayTool(); }
                        );
                        okButton.bind("click", {
                           field : textField, createNew : createNew, node : node}
                        , insertClickHandler);
                        me.displayInterface.displayTool( {
                           content : panel, handler : editorClosed, offset : 130}
                        );
                        editorShown = true;
                        }
                     function closeEditor() {
                        if(editorShown) {
                           me.displayInterface.displayTool();
                           }
                        }
                     function editorClosed() {
                        editorShown = false;
                        }
                     function insertClickHandler(event) {
                        var textField = event.data.field;
                        var createNew = event.data.createNew;
                        var node = event.data.node;
                        var laTeXCode = textField.val();
                        renderLatex(laTeXCode, node, createNew);
                        closeEditor();
                        }
                     function convertLaTeXHandler(event) {
                        var body = me.displayInterface.body;
                        var textNodes = $("*", body).add(body).contents().filter(function() {
                           return this.nodeType == 3; }
                        );
                        var tasks = [];
                        textNodes.each(function() {
                           var i, j; var pairs = [["\\[", "\\]"], ["\\(", "\\)"], ["$", "$", "\\"]]; var proceed = true; var node = this; while(proceed) {
                              proceed = false; pairs.each(function() {
                                 var pre = this[0]; var post = this[1]; i = node.nodeValue.indexOf(pre); if(i >- 1) {
                                    j = i + 1; if(pre == "$" && node.nodeValue.substr(i, 2) == "$$") {
                                       pre = post = "$$"; j++; }
                                    do {
                                       j = node.nodeValue.indexOf(post, (i > j ? i : j) + 1); }
                                    while(j >- 1 && this.length == 3 && node.nodeValue.substr(j - 1, 1) == this[2])if(j >- 1) {
                                       var code = node.nodeValue.slice(i, j + post.length); var newNode = node.splitText(i); var newText = newNode.nodeValue; newText = newText.substring(j - i + post.length); if(newText.length == 0) {
                                          node.parentNode.removeChild(newNode); }
                                       else {
                                          newNode.nodeValue = newText; }
                                       var dummy = me.displayInterface.document.createElement("span"); node.parentNode.insertBefore(dummy, newNode = node.nextSibling); tasks.push([code, $(dummy), true]); if((node = newNode) != null && node.nodeType == 3) {
                                          proceed = true; }
                                       return false; }
                                    }
                                 return true; }
                              ); }; }
                        );
                        if(tasks.length > 0) {
                           tasks.each(function() {
                              renderLatex.apply(me, this); }
                           );
                           }
                        else {
                           alert(lang.AUTO_LATEX_NO_JOBS);
                           }
                        }
                     function renderLatex(code, node, createNew) {
                        var obj = {
                           source : code, node : node, createNew : createNew === true};
                        me.displayInterface.server.renderEquation(code, me.displayInterface.getCard().step.topic, utility.createCallback(obj, renderSuccess));
                        }
                     function renderSuccess(data) {
                        var tag = this;
                        if(!data.success) {
                           switch(data.errorcode) {
                              case 1 : alert(lang.FORMULA_TOO_LONG);
                              break;
                              case 2 : alert(lang.FORMULA_CONTAINS_BLACKLISTED);
                              break;
                              case 3 : case 4 : alert(lang.RENDERING_FAILED);
                              break;
                              case 5 : alert(lang.FORMULA_TOO_BIG);
                              break;
                              case 6 : alert(lang.COULDNT_COPY_IMAGE);
                              break;
                              case 7 : alert(lang.ERROR_QUOTA);
                              break;
                              default : alert(lang.OTHER_ERROR);
                              }
                           }
                        else {
                           var isBlock = (tag.source.indexOf("$$") >- 1 || (tag.source.indexOf("\\[") >- 1 && tag.source.indexOf("\\]") >- 1) || tag.source.indexOf("\\begin{") >- 1);
                           var img;
                           if(tag.createNew) {
                              img = $(me.displayInterface.document.createElement("img")).click(equationClickHandler);
                              }
                           else {
                              img = tag.node;
                              }
                           img.attr( {
                              src : ""}
                           );
                           img.attr( {
                              src : data.filename, border : "0"}
                           );
                           try {
                              img.attr("alt", tag.source);
                              }
                           catch(e) {
                              img.attr("alt", lang.LATEX_SOURCE);
                              }
                           if(!img.hasClass("equation")) {
                              img.addClass("equation");
                              $.fn[isBlock ? "addClass" : "removeClass"].call(img, "block");
                              }
                           if(tag.createNew) {
                              img.insertBefore(tag.node);
                              img.selectNodes( {
                                 collapse : true}
                              );
                              tag.node.remove();
                              }
                           me.displayInterface.triggerChange();
                           }
                        }
                     this.unload = function() {
                        me = null;
                        }
                     }
                  client.pageplugins.displayplugins.LaTeXmage.prototype = new client.pageplugins.NoteDisplayPlugin;
                  client.pageplugins.CardTrainer = function(args) {
                     client.PagePlugin.call(this);
                     var floatObj = undefined;
                     var currentFlashCards;
                     this.trainCards = function(cards) {
                        completedCards = null;
                        currentFlashCards = cards;
                        if(currentFlashCards.length == 0) {
                           alert(lang.NO_FLASH_CARDS);
                           return;
                           }
                        this.gotoNextCard(null, null);
                        }
                     var queue, completedCards;
                     this.gotoNextCard = function(answer, directionBackwards, specificCard, noScroll) {
                        var minLevel = this.page.server.minBoxLevel;
                        var maxLevel = this.page.server.maxBoxLevel;
                        var explanation = undefined;
                        if(answer != null && currentFlashCard) {
                           var currentFlashCardBox = currentFlashCard.box;
                           switch(answer) {
                              case client.data.FlashCardResultConstants.right : if(currentFlashCard.level == maxLevel) {
                                 if(!completedCards) {
                                    completedCards = [];
                                    }
                                 this.page.server.moveFlashCardToLevel(currentFlashCard, minLevel);
                                 completedCards.push(currentFlashCard);
                                 currentFlashCards.remove(currentFlashCard);
                                 explanation = [boxes[3], "removed"];
                                 }
                              else {
                                 explanation = [boxes[currentFlashCardBox.level], "up"];
                                 }
                              this.page.server.moveFlashCardToLevel(currentFlashCard, currentFlashCardBox.level + 1);
                              break;
                              case client.data.FlashCardResultConstants.neutral : explanation = [boxes[(currentFlashCardBox.level > 1) ? (currentFlashCardBox.level - 2) : 0], "down"];
                              this.page.server.moveFlashCardToLevel(currentFlashCard, currentFlashCardBox.level - 1);
                              break;
                              case client.data.FlashCardResultConstants.wrong : explanation = [boxes[0], "revert"];
                              this.page.server.moveFlashCardToLevel(currentFlashCard, minLevel);
                              break;
                              case client.data.FlashCardResultConstants.none : break;
                              }
                           }
                        if(currentFlashCards.length == 0) {
                           alert(lang.SESSION_FINISHED);
                           updateBoxes.apply(this, [currentFlashCards, noScroll]);
                           unloadUI();
                           currentFlashCard = null;
                           return;
                           }
                        var flashCard = specificCard;
                        if(!flashCard) {
                           if(!queue || queue.length == 0) {
                              var numOfCards = currentFlashCards.length;
                              var numOfCardsInBoxes = [];
                              for(var i = 0; i <= maxLevel - minLevel; i++) {
                                 numOfCardsInBoxes.push(0);
                                 }
                              for(var i = 0; i < numOfCards; i++) {
                                 if(currentFlashCards[i] != currentFlashCard) {
                                    numOfCardsInBoxes[currentFlashCards[i].level - minLevel]++;
                                    }
                                 }
                              var start = positionOfMaximum(numOfCardsInBoxes);
                              var cardBoxes = this.page.server.getFlashCardBoxes();
                              var box = cardBoxes[start];
                              queue = [];
                              var count = Math.min(10, Math.max(Math.floor(numOfCards / 5), 5));
                              for(var j = 0; j < box.getCardCount(); j++) {
                                 var card = box.getCard(j);
                                 if(currentFlashCards.indexOf(card) >- 1 && (card !== currentFlashCard || queue.length > 0)) {
                                    queue.push(card);
                                    count--;
                                    if(count == 0) {
                                       break;
                                       }
                                    }
                                 }
                              if(queue.length == 0 && currentFlashCard) {
                                 queue.push(currentFlashCard);
                                 }
                              if(perm % 2 == 1 && queue.length > 0 && queue[queue.length - 1] !== currentFlashCard) {
                                 queue.reverse();
                                 }
                              perm++;
                              }
                           flashCard = queue.shift();
                           }
                        unloadUI();
                        if(flashCard) {
                           loadUI( {
                              flashCard : flashCard, topicName : flashCard.marker.card.step.topic.title, cardLevel : flashCard.level, callback : utility.createCallback(this, this.gotoNextCard)}
                           );
                           updateBoxes.apply(this, [currentFlashCards, noScroll]);
                           if(explanation !== undefined) {
                              explainResult.apply(this, explanation);
                              }
                           currentFlashCard = flashCard;
                           refreshContext();
                           }
                        return;
                        function positionOfMaximum(numOfCardsInBoxes) {
                           var numOfBoxes = numOfCardsInBoxes.length;
                           var max = numOfCardsInBoxes[0];
                           var index = 0;
                           for(var i = 0; i < numOfBoxes; i++) {
                              if(max < numOfCardsInBoxes[i]) {
                                 max = numOfCardsInBoxes[i];
                                 index = i;
                                 }
                              }
                           return index;
                           }
                        }
                     this.endTraining = function() {
                        if(node != null) {
                           destroyUI();
                           }
                        currentFlashCards = null;
                        }
                     var balloonConfig, currentBalloon, perm;
                     this.load = function() {
                        client.PagePlugin.prototype.load.call(this);
                        setupUI.call(this);
                        var currentUser = this.page.server.getCurrentUser();
                        var settings = currentUser.settings;
                        if(settings.trainerBalloons === undefined) {
                           settings.trainerBalloons = {
                              };
                           }
                        balloonConfig = settings.trainerBalloons;
                        perm = 0;
                        };
                     var mainView;
                     this.onPluginLoad = function(plugin) {
                        if(plugin instanceof client.pageplugins.MainView) {
                           mainView = plugin;
                           }
                        }
                     this.onPluginRemove = function(plugin) {
                        if(plugin === mainView) {
                           delete mainView;
                           }
                        }
                     function setupUI() {
                        var minLevel = this.page.server.minBoxLevel;
                        var maxLevel = this.page.server.maxBoxLevel;
                        node = $(document.createElement("div")).appendTo(args.container);
                        node.append($(document.createElement("h1")).text(lang.TRAINER_HEADING));
                        label = $(document.createElement("p")).appendTo(node).hide();
                        maincont = $(document.createElement("div")).addClass("flashcarddisplay").appendTo(node);
                        toolbar = utility.createToolbar().appendTo(node);
                        boxes = [];
                        var totalWidth = maincont.width() - (maxLevel - minLevel) * 7 - 2 + 10;
                        if(totalWidth < 50) {
                           totalWidth = 50;
                           }
                        $(document.createElement("h3")).text(lang.FLASH_CARD_BOXES).appendTo(node);
                        for(var i = minLevel; i <= maxLevel; i++) {
                           boxes.push($(document.createElement("div")).addClass("flashcardbox").css("width", Math.floor(totalWidth / (maxLevel - minLevel + 1))).css("margin-right", i < maxLevel ? "5px" : "").appendTo(node));
                           }
                        }
                     function explainResult(box, direction) {
                        hideBalloon();
                        currentBalloon = $.showBalloon(box, lang.BALLOON_TEXTS.TRAINER["ANSWER_" + direction.toUpperCase()], jQuery.balloon.BalloonPositionConstants.bottomMiddle, undefined, balloonConfig, "result" + direction);
                        return currentBalloon;
                        }
                     function hideBalloon() {
                        if(currentBalloon) {
                           currentBalloon.remove();
                           currentBalloon = null;
                           }
                        }
                     this.unload = function() {
                        hideBalloon();
                        this.endTraining();
                        client.PagePlugin.prototype.unload.call(this);
                        };
                     var node;
                     var maincont;
                     var label;
                     var cardtitle;
                     var cardcont;
                     var toolbar;
                     var boxes;
                     var callback;
                     var currentFlashCards;
                     function updateBoxes(cards, noScroll) {
                        if(cards) {
                           currentFlashCards = cards;
                           }
                        var minLevel = this.page.server.minBoxLevel;
                        var maxLevel = this.page.server.maxBoxLevel;
                        for(var i = 0; i <= maxLevel - minLevel; i++) {
                           boxes[i].css("height", "").children().remove();
                           boxes[i][(currentFlashCard.level == i + minLevel) ? "addClass" : "removeClass"].call(boxes[i], "selected");
                           }
                        var height;
                        if(boxes.length > 0) {
                           height = boxes[0].height();
                           }
                        for(var i = 0; i < currentFlashCards.length; i++) {
                           var flashCard = currentFlashCards[i];
                           var box = boxes[flashCard.level - minLevel];
                           var cardLine;
                           var title = flashCard.title;
                           box.append(cardLine = $(document.createElement("div")).addClass("flashcard"));
                           if(title instanceof String) {
                              cardLine.text(title);
                              }
                           else {
                              cardLine.append(title);
                              }
                           if(currentFlashCard == flashCard) {
                              cardLine.addClass("selected");
                              }
                           else {
                              cardLine.addClass("pointer").bind("click", flashCard, function(event) {
                                 var flashCard = event.data; utility.triggerCallback(callback, [client.data.FlashCardResultConstants.none, null, flashCard]); }
                              );
                              }
                           if(lastFlashCard == flashCard) {
                              cardLine.addClass("last");
                              }
                           }
                        if(!noScroll) {
                           var selected = $("div.flashcard.last", node).add($("div.flashcard.selected", node));
                           selected.each(function() {
                              var cardLine = $(this); var box = cardLine.parent(); box.get(0).scrollTop = cardLine.get(0).offsetTop + cardLine.height() / 2 - box[0].clientHeight / 2; }
                           );
                           }
                        var orgHeight = height;
                        for(var i = 0; i <= maxLevel - minLevel; i++) {
                           var tHeight = boxes[i].height();
                           height = tHeight > height ? tHeight : height;
                           }
                        if(height != orgHeight) {
                           for(var i = 0; i <= maxLevel - minLevel; i++) {
                              boxes[i].css("height", height);
                              }
                           }
                        }
                     var currentContent;
                     var currentFlashCard;
                     var lastFlashCard;
                     this.getCurrentFlashCard = function() {
                        return currentFlashCard || lastFlashCard;
                        }
                     function loadUI(fargs) {
                        callback = fargs.callback;
                        currentFlashCard = fargs.flashCard;
                        var marker = currentFlashCard.marker;
                        currentContent = marker.getFlashCardContents(currentFlashCard);
                        cardtitle = $(document.createElement("div")).appendTo(maincont);
                        label.contents().remove();
                        label.append(document.createTextNode(lang.GENERAL_PREFIX));
                        label.append($(document.createElement("b")).text(currentFlashCard.marker.card.title));
                        label.append(document.createTextNode(lang.TOPIC_BRIDGE));
                        label.append($(document.createElement("b")).text(fargs.topicName));
                        label.append(document.createTextNode(lang.BOX_LEVEL_BRIDGE));
                        label.append($(document.createElement("b")).text(fargs.cardLevel));
                        label.append(document.createTextNode("."));
                        cardcont = $(document.createElement("div")).appendTo(maincont);
                        if(currentContent.frontSide) {
                           showFrontSide();
                           }
                        else {
                           showBackSide();
                           }
                        };
                     function buttonHandler(event) {
                        var button = event.data;
                        switch(button.action) {
                           case"gotoAnswer" : hideBalloon();
                           showBackSide();
                           break;
                           case"gotoQuestion" : showFrontSide();
                           break;
                           case"showContext" : if(mainView !== undefined) {
                              showContext();
                              }
                           break;
                           }
                        }
                     function showContext() {
                        if(floatObj === undefined) {
                           floatObj = mainView.requestFloat(floatCloseHandler, undefined, undefined, undefined, true);
                           hideBalloon();
                           }
                        refreshContext();
                        }
                     function refreshContext() {
                        if(floatObj !== undefined) {
                           floatObj.content.children().not(":first").not(":first").remove();
                           appendContext(floatObj.content);
                           }
                        }
                     function floatCloseHandler() {
                        floatObj = undefined;
                        }
                     function showFrontSide() {
                        cardcont.contents().remove();
                        currentContent.frontSide.appendTo(cardcont);
                        toolbar.children().remove();
                        utility.createMenuItem("train.png", "gotoAnswer", lang.GOTO_ANSWER, buttonHandler, null).attr("accesskey", "a").appendTo(toolbar);
                        if(!currentContent.showCard) {
                           utility.createToolbarSeparator().appendTo(toolbar);
                           utility.createMenuItem("study-tool.png", "showContext", lang.SHOW_CONTEXT, buttonHandler, null).attr("accesskey", "c").appendTo(toolbar);
                           }
                        }
                     function showBackSide() {
                        if(currentContent.backSide) {
                           cardcont.contents().remove();
                           currentContent.backSide.appendTo(cardcont);
                           $("[id]", cardcont).removeAttr("id");
                           }
                        if(currentContent.showCard) {
                           if(mainView !== undefined) {
                              showContext();
                              }
                           else {
                              appendContext(cardcont);
                              }
                           }
                        if(!currentContent.backSide &&!currentContent.showCard) {
                           return;
                           }
                        toolbar.children().remove();
                        if(currentContent.showSelfAssessmentButtons) {
                           var assessmentBar = toolbar;
                           var rightButton = utility.createMenuItem("face-smile.png", "answerRight", currentContent.showCard ? lang.REVIEW_RIGHT : lang.ANSWER_RIGHT, assessmentButtonHandler, null).attr("accesskey", "r").appendTo(assessmentBar);
                           var unsureButton = utility.createButton("face-plain.png", "answerUnsure", currentContent.showCard ? lang.REVIEW_UNSURE : lang.ANSWER_UNSURE, assessmentButtonHandler, null).attr("accesskey", "e").appendTo(assessmentBar);
                           var wrongButton = utility.createMenuItem("face-sad.png", "answerWrong", currentContent.showCard ? lang.REVIEW_WRONG : lang.ANSWER_WRONG, assessmentButtonHandler, null).attr("accesskey", "w").appendTo(assessmentBar);
                           utility.createToolbarSeparator().appendTo(assessmentBar);
                           }
                        if(currentContent.frontSide &&!currentContent.showCard) {
                           utility.createMenuItem("train.png", "gotoQuestion", lang.GOTO_QUESTION, buttonHandler, null).attr("accesskey", "q").appendTo(toolbar);
                           }
                        if(!currentContent.showCard) {
                           utility.createMenuItem("study-tool.png", "showContext", lang.SHOW_CONTEXT, buttonHandler, null).attr("accesskey", "c").appendTo(toolbar);
                           }
                        }
                     function appendContext(container) {
                        var card = currentFlashCard.marker.card;
                        $(document.createElement("h1")).text(lang.CONTEXT + ": ").append($(document.createElement("em")).text(currentFlashCard.marker.card.title)).appendTo(container);
                        var wrapper = $(document.createElement("div")).addClass("context-display").appendTo(container);
                        var display = $(document.createElement("div")).html(card.content).appendTo(wrapper);
                        $("a", display).attr("target", "_blank");
                        var range = $.fromRangeString(currentFlashCard.marker.rangeStore, container[0]);
                        range.css("background-color", "#E1E411");
                        var top = utility.getAbsolutePosition(range[0]).top - utility.getAbsolutePosition(display[0]).top;
                        display[0].scrollTop = top > 20 ? (top - 20) : 0;
                        return wrapper;
                        }
                     function assessmentButtonHandler(event) {
                        var button = event.data;
                        var answer = client.data.FlashCardResultConstants.none;
                        switch(button.action) {
                           case"answerRight" : answer = client.data.FlashCardResultConstants.right;
                           break;
                           case"answerUnsure" : answer = client.data.FlashCardResultConstants.neutral;
                           break;
                           case"answerWrong" : answer = client.data.FlashCardResultConstants.wrong;
                           break;
                           }
                        currentFlashCard.addResult(answer);
                        var now = new Date();
                        currentFlashCard.lastTimeAnswered = now.getTime();
                        utility.triggerCallback(callback, answer);
                        }
                     function unloadUI() {
                        if(cardtitle) {
                           cardtitle.remove();
                           }
                        cardtitle = null;
                        if(cardcont) {
                           cardcont.remove();
                           }
                        cardcont = null;
                        toolbar.children().remove();
                        lastFlashCard = currentFlashCard;
                        currentContent = currentFlashCard = null;
                        };
                     function destroyUI() {
                        if(cardcont) {
                           unloadUI();
                           }
                        label.remove();
                        label = null;
                        for(var i = 0; i < boxes.length; i++) {
                           boxes[i].remove();
                           }
                        boxes = currentFlashCards = lastFlashCard = null;
                        maincont.remove();
                        maincont = null;
                        toolbar.remove();
                        toolbar = null;
                        node.remove();
                        node = null;
                        };
                     }
                  client.pageplugins.CardTrainer.prototype = new client.PagePlugin;
                  client.Marker = function() {
                     this.relationsStore = [];
                     this.relations = [];
                     this.dataStore = {
                        };
                     this.data = {
                        };
                     this.flashCards = [];
                     }
                  client.Marker.prototype.createMenuItems = function(createItem) {
                     };
                  client.Marker.prototype.loading = function(args) {
                     args.cancel = false;
                     };
                  client.Marker.prototype.load = function(args) {
                     return;
                     };
                  client.Marker.prototype.update = function(args) {
                     args.handled = false;
                     return;
                     };
                  client.Marker.prototype.onSelect = function() {
                     return;
                     };
                  client.Marker.prototype.onDeselect = function() {
                     return;
                     };
                  client.Marker.prototype.unload = function() {
                     return;
                     };
                  client.Marker.prototype.destroy = function() {
                     return;
                     };
                  client.Marker.prototype.createNew = function() {
                     return new client.markers[utility.camelCase(this.tool) + "Marker"]();
                     };
                  client.Marker.prototype.getFlashCardContents = function(flashCard) {
                     return;
                     };
                  client.Marker.prototype.prepareTraining = function() {
                     return;
                     };
                  client.Marker.prototype.endTraining = function() {
                     return;
                     };
                  client.Marker.prototype.hasChanged = false;
                  client.Marker.prototype.id = null;
                  client.Marker.prototype.tool = null;
                  client.Marker.prototype.dataStore = undefined;
                  client.Marker.prototype.data = undefined;
                  client.Marker.prototype.revision = 0;
                  client.Marker.prototype.startMarker = null;
                  client.Marker.prototype.endMarker = null;
                  client.Marker.prototype.rangeStore = null;
                  client.Marker.prototype.range = null;
                  client.Marker.prototype.card = null;
                  client.Marker.prototype.flashCards = undefined;
                  client.Marker.prototype.relationsStore = undefined;
                  client.Marker.prototype.relations = undefined;
                  client.Marker.prototype.clearRelations = function() {
                     this.relationsStore = [];
                     }
                  client.Marker.prototype.displayInterface = null;
                  client.Marker.prototype.isMine = undefined;
                  client.Marker.prototype.shared = false;
                  client.Marker.prototype.notify = false;
                  client.Marker.prototype.user = false;
                  client.Marker.prototype.transformStatic = function(returnChanges) {
                     var markerRestore = {
                        tool : this.tool, range : returnChanges ? this.range.toRangeString() : this.rangeStore, shared : this.shared, notify : this.notify, revision : this.revision}
                     if(returnChanges) {
                        if(this.hasChanged && this.data !== undefined) {
                           markerRestore.data = JSON.stringify(this.data);
                           }
                        if(this.hasChanged) {
                           markerRestore.relations = [];
                           this.relations.each(function() {
                              markerRestore.relations.push(this.clone()); }
                           );
                           }
                        }
                     else {
                        markerRestore.data = JSON.stringify(this.dataStore);
                        }
                     if(this.id) {
                        markerRestore.id = this.id;
                        }
                     return markerRestore;
                     }
                  client.Marker.prototype.restore = function() {
                     this.data = jQuery.extend( {
                        }
                     , this.dataStore);
                     this.relations = [];
                     if(this.relationsStore) {
                        var marker = this;
                        this.relationsStore.each(function() {
                           marker.relations.push(this.clone()); }
                        );
                        }
                     }
                  client.Marker.prototype.clone = function() {
                     var newMarker = this.createNew();
                     newMarker.dataStore = this.data;
                     newMarker.relationsStore = this.relations;
                     newMarker.restore();
                     delete newMarker.dataStore;
                     delete newMarker.relationsStore;
                     newMarker.notify = this.notify;
                     newMarker.shared = this.shared;
                     newMarker.hasChanged = true;
                     return newMarker;
                     }
                  client.Marker.prototype.trashable = function() {
                     return false;
                     };
                  client.Marker.prototype.trainable = function() {
                     return false;
                     };
                  client.Marker.prototype.createTrashMenuItem = function() {
                     return null;
                     };
                  client.Marker.prototype.adjustClass = function() {
                     var classes = ["marker"];
                     if(this.isMine) {
                        classes.push("mine");
                        }
                     this.range.addClass(classes.join(" "));
                     }
                  client.Marker.checkTrainable = function(markers) {
                     var trainable = false;
                     markers.each(function() {
                        if(this.trainable()) {
                           trainable = true; return false; }
                        }
                     );
                     return trainable;
                     }
                  client.markers.QuestionModeConstants = {
                     question : 0, annotation : 1, definition : 2, issue : 3}
                  client.markers.QuestionMarker = function() {
                     var MAX_TEXT_SIZE = 1536;
                     client.Marker.call(this);
                     this.tool = "question";
                     this.shared = true;
                     this.createMenuItems = function(createItem) {
                        var annotationTemplate = new client.markers.QuestionMarker();
                        annotationTemplate.data.questionMode = client.markers.QuestionModeConstants.annotation;
                        createItem(lang.ANNOTATE, "dialog-information.png", annotationTemplate).attr("accesskey", "a");
                        var problemTemplate = new client.markers.QuestionMarker();
                        problemTemplate.data.questionMode = client.markers.QuestionModeConstants.issue;
                        problemTemplate.notify = true;
                        createItem(lang.ASK_QUESTION, "problem-marker.png", problemTemplate).attr("accesskey", "p");
                        var relatedQuestionTemplate = new client.markers.QuestionMarker();
                        relatedQuestionTemplate.data.questionMode = client.markers.QuestionModeConstants.question;
                        createItem(lang.ANSWER_LINK, "dialog-question.png", relatedQuestionTemplate).attr("accesskey", "q");
                        var defineTemplate = new client.markers.QuestionMarker();
                        defineTemplate.data.questionMode = client.markers.QuestionModeConstants.definition;
                        defineTemplate.takeQuestionFromSelection = true;
                        createItem(lang.DEFINE_SELECTION, "definition.png", defineTemplate).attr("accesskey", "d");
                        }
                     var removeOnCancel;
                     var me;
                     this.load = function() {
                        me = this;
                        this.startMarker = utility.createButton(getIcon(this.data.questionMode, false), "", "", null).css( {
                           width : 16, height : 16}
                        ).bind("click", this, markerClick).bind("contextmenu", markerClick);
                        if((this.data.question == null && this.data.questionMode != client.markers.QuestionModeConstants.annotation) || (this.data.answer == null && this.data.questionMode == client.markers.QuestionModeConstants.annotation)) {
                           if(this.takeQuestionFromSelection === true) {
                              this.data.question = this.range.htmlWithStructure(function() {
                                 $(this).removeClass("marker highlight mine red"); }
                              );
                              }
                           removeOnCancel = true;
                           }
                        updateMarker();
                        }
                     function updateMarker() {
                        var editable = true;
                        me.startMarker.css("background-image", "url('" + config.imagePath + getIcon(me.data.questionMode, false) + "')");
                        var title;
                        if(me.data.questionMode != client.markers.QuestionModeConstants.annotation && me.data.question != null) {
                           title = getTitle(me.data.questionMode) + ": " + $("<div>" + me.data.question + "</div>").text();
                           }
                        else if(me.data.questionMode == client.markers.QuestionModeConstants.annotation && me.data.answer != null) {
                           title = getTitle(me.data.questionMode) + ": " + $("<div>" + me.data.answer + "</div>").text();
                           }
                        else {
                           title = (editable ? lang.QUESTION_EDIT : lang.QUESTION_VIEW) + getTitle(me.data.questionMode);
                           }
                        if(title.length > 50) {
                           title = title.substr(0, 50) + "...";
                           }
                        me.startMarker.attr("title", title);
                        if(me.range &&!me.displayInterface.isEditor) {
                           me.range.attr("title", title);
                           }
                        me.adjustClass();
                        }
                     function markerClick(event) {
                        event.preventDefault();
                        event.stopPropagation();
                        if(!me.selected) {
                           me.displayInterface.selectMarker(me);
                           }
                        else if(editor === undefined) {
                           showEditor();
                           }
                        else if(editor !== undefined) {
                           $("a.marker-button", editor).triggerHandler("click");
                           }
                        }
                     this.onSelect = function() {
                        showEditor();
                        }
                     this.adjustClass = function() {
                        client.Marker.prototype.adjustClass.call(this);
                        if(this.data.questionMode == client.markers.QuestionModeConstants.issue) {
                           this.range.addClass("red");
                           }
                        else {
                           this.range.removeClass("red");
                           }
                        }
                     var questionEditor;
                     var answerEditor;
                     var editor;
                     function showEditor() {
                        var editable = true;
                        var form = $(document.createElement("div"));
                        var titleField = $(document.createElement("h3"));
                        var questionModeSelector, questionModeRow, sharingModeRow;
                        if(editable) {
                           questionModeRow = utility.createToolbar().appendTo(form);
                           $(document.createElement("span")).text(lang.QUESTION_MODE).appendTo(questionModeRow);
                           questionModeSelector = $(document.createElement("select")).attr("name", "questionMode").appendTo(questionModeRow);
                           $(document.createElement("option")).val("0").text(lang.QUESTION).appendTo(questionModeSelector);
                           $(document.createElement("option")).val("1").text(lang.ANNOTATION).appendTo(questionModeSelector);
                           $(document.createElement("option")).val("2").text(lang.DEFINITION).appendTo(questionModeSelector);
                           $(document.createElement("option")).val("3").text(lang.ISSUE).appendTo(questionModeSelector);
                           questionModeSelector.val(me.data.questionMode.toString());
                           }
                        else {
                           titleField.text(getTitle(me.data.questionMode) + " " + lang.STUDY_TOOL_TITLE);
                           }
                        titleField.appendTo(form);
                        var questionBox = $(document.createElement("div")).appendTo(form);
                        questionEditor = new client.widgets.Editor( {
                           node : questionBox}
                        );
                        var answerBox = $(document.createElement("div"));
                        answerBox.appendTo(form);
                        var toolbar = utility.createToolbar().appendTo(form);
                        if(editable) {
                           sharingModeRow = $(document.createElement("div")).addClass("float", "left");
                           if(me.isMine) {
                              var shareCheck = $(document.createElement("input")).attr( {
                                 "id" : "questionShareCheck", "type" : "checkbox"}
                              ).appendTo(sharingModeRow);
                              shareCheck.get(0).checked = me.shared;
                              shareCheck.change(shareChangeHandler);
                              var shareLabel = $(document.createElement("label")).attr("for", "questionShareCheck").text(lang.SHARE).appendTo(sharingModeRow);
                              }
                           else {
                              sharingModeRow.text(lang.SHARED_BY + ": " + me.user.name);
                              }
                           toolbar.append(sharingModeRow);
                           }
                        else {
                           if(me.data.questionMode == client.markers.QuestionModeConstants.annotation) {
                              questionBox.css("display", "none");
                              }
                           }
                        utility.createMenuItem("dialog-apply.png", "ok", lang.SAVE, dialogHandler).attr("accesskey", "o").addClass("right").appendTo(toolbar);
                        if(me.data.questionMode == client.markers.QuestionModeConstants.issue && me.isMine && me.data.answer) {
                           utility.createMenuItem("thumbs-up.png", "convertproblem", lang.PROBLEM_SOLVED, dialogHandler).addClass("right").appendTo(toolbar);
                           }
                        utility.createMenuItem("process-stop.png", "cancel", lang.CANCEL, dialogHandler).attr("accesskey", "n").addClass("right").appendTo(toolbar);
                        if(!config.reduceFeatureSet &&!me.displayInterface.getCard().isShared()) {
                           $(document.createElement("p")).html(lang.NEEDS_SHARING_TO_GET_SOLVED_HTML).appendTo(form);
                           }
                        answerEditor = new client.widgets.Editor( {
                           node : answerBox}
                        );
                        var isNewProblem = removeOnCancel && me.data.questionMode == client.markers.QuestionModeConstants.issue;
                        editor = me.displayInterface.displayTool( {
                           content : form, handler : dialogClosedHandler, offset : isNewProblem ? 200 : undefined}
                        );
                        additionalItems = {
                           11 : utility.createButton("edit-paste.png", "pasteinquestion", lang.PASTE_CURRENT_SELECTION, pasteSelectionHandler)}
                        if(editable) {
                           questionEditor.setupToolbar( {
                              noUndo : true, additionalItems : additionalItems}
                           );
                           }
                        questionEditor.load( {
                           content : me.data.question, isEditor : editable, callback : utility.createCallback(me, afterLoadQuestion), css : {
                              width : "100%", height : 120, margin : 0, "background-color" : "#fff"}
                           }
                        );
                        if(questionModeSelector) {
                           questionModeSelector.bind("change", {
                              answerBox : answerBox, questionBox : questionBox, titleField : titleField}
                           , questionModeChangeHandler);
                           questionModeSelector.triggerHandler("change");
                           }
                        questionBox.css( {
                           "clear" : "both", "margin-right" : 10, "float" : "left", width : isNewProblem ? 290 : 240}
                        );
                        additionalItems = {
                           11 : utility.createButton("edit-paste.png", "pasteinanswer", lang.PASTE_CURRENT_SELECTION, pasteSelectionHandler)}
                        if(editable) {
                           answerEditor.setupToolbar( {
                              noUndo : true, additionalItems : additionalItems}
                           );
                           }
                        answerEditor.load( {
                           content : me.data.answer, isEditor : editable, callback : utility.createCallback(me, afterLoadAnswer), css : {
                              width : "100%", height : 120, margin : 0, "background-color" : "#fff"}
                           }
                        );
                        if(editable) {
                           if(isNewProblem) {
                              answerBox.hide();
                              }
                           if(removeOnCancel || (me.data.questionMode == client.markers.QuestionModeConstants.issue &&!me.isMine)) {
                              questionModeRow.hide();
                              }
                           else {
                              questionModeRow.css( {
                                 clear : "none", "float" : "right", "margin-top" : 5}
                              );
                              }
                           }
                        }
                     function pasteSelectionHandler(event) {
                        if(event.data.action == "pasteinquestion") {
                           pasteSelection( {
                              dummy : questionDummy, editor : questionEditor}
                           );
                           }
                        if(event.data.action == "pasteinanswer") {
                           pasteSelection( {
                              dummy : answerDummy, editor : answerEditor}
                           );
                           }
                        function pasteSelection(params) {
                           var dummy = params.dummy;
                           var editor = params.editor;
                           if(dummy && dummy.is(":visible") &&!dummy.removed) {
                              $(editor.document).triggerHandler("click");
                              var noteSheetSelection = me.range.htmlWithStructure(function() {
                                 $(this).removeClass("marker highlight mine red"); }
                              );
                              editor.body.html(noteSheetSelection);
                              }
                           else {
                              var noteSheetSelection = me.range.htmlWithStructure(function() {
                                 $(this).removeClass("marker highlight mine red"); }
                              );
                              var fieldEditorSelection = $.getSelection( {
                                 context : editor.document}
                              );
                              $(editor.document.createElement("div")).html(noteSheetSelection).contents().insertBefore(fieldEditorSelection[0]);
                              fieldEditorSelection.remove();
                              }
                           }
                        }
                     function shareChangeHandler(event) {
                        me.shared = this.checked;
                        me.hasChanged = true;
                        }
                     function questionModeChangeHandler(event) {
                        var tag = event.data;
                        var mode = parseInt($(this).val());
                        ;
                        if(me.data.questionMode != mode) {
                           me.data.questionMode = mode;
                           me.notify = (me.data.questionMode == client.markers.QuestionModeConstants.issue);
                           me.hasChanged = true;
                           me.displayInterface.triggerChange();
                           }
                        updateMarker();
                        tag.titleField.text(getTitle(me.data.questionMode));
                        if(questionDummy && questionDummy.is(":visible") &&!questionDummy.removed) {
                           questionDummy.text(getQuestionDummy(me.data.questionMode));
                           }
                        if(answerDummy && answerDummy.is(":visible") &&!answerDummy.removed) {
                           answerDummy.text(getAnswerDummy(me.data.questionMode));
                           }
                        var showQuestionBox = me.data.questionMode != client.markers.QuestionModeConstants.annotation;
                        tag.questionBox.css("display", showQuestionBox ? "" : "none");
                        if(!showQuestionBox) {
                           tag.answerBox.css( {
                              "float" : "none", width : "auto"}
                           );
                           }
                        else {
                           tag.answerBox.css( {
                              "float" : "left", width : 240}
                           );
                           }
                        }
                     function getQuestionDummy(questionMode, getError) {
                        getError = (getError === true);
                        switch(questionMode) {
                           case client.markers.QuestionModeConstants.question : return getError ? lang.NO_QUESTION : lang.ENTER_QUESTION;
                           case client.markers.QuestionModeConstants.definition : return getError ? lang.NO_EXPRESSION : lang.ENTER_EXPRESSION;
                           case client.markers.QuestionModeConstants.issue : return getError ? lang.NO_QUESTION : lang.DESCRIBE_ISSUE;
                           default : return"";
                           }
                        }
                     function getAnswerDummy(questionMode, getError) {
                        getError = (getError === true);
                        switch(questionMode) {
                           case client.markers.QuestionModeConstants.question : return getError ? lang.NO_ANSWER : lang.ENTER_ANSWER;
                           case client.markers.QuestionModeConstants.annotation : return getError ? lang.NO_ANNOTATION : lang.ENTER_ANNOTATION;
                           case client.markers.QuestionModeConstants.definition : return getError ? lang.NO_DEFINITION : lang.ENTER_DEFINITION;
                           case client.markers.QuestionModeConstants.issue : return getError ? lang.NO_ANSWER : lang.SUGGEST_ANSWER;
                           default : return"";
                           }
                        }
                     var questionDummy;
                     function afterLoadQuestion() {
                        if(this.data.question == null) {
                           questionDummy = createRemovable(questionEditor, getQuestionDummy(this.data.questionMode));
                           questionEditor.body.one("change", questionDummy, dummyNodeClickHandler);
                           }
                        questionEditor.window.focus();
                        }
                     var answerDummy;
                     function afterLoadAnswer() {
                        if(this.data.answer == null) {
                           answerDummy = createRemovable(answerEditor, getAnswerDummy(this.data.questionMode));
                           answerEditor.body.one("change", answerDummy, dummyNodeClickHandler);
                           }
                        }
                     function createRemovable(editor, text) {
                        editor.body.contents().remove();
                        var node = $(editor.document.createElement("p")).text(text).appendTo(editor.body);
                        $(editor.document).one("click", node, dummyNodeClickHandler);
                        return node;
                        }
                     function dummyNodeClickHandler(event) {
                        event.stopPropagation();
                        var node = event.data;
                        if(node.removed ||!node.is(":visible")) {
                           return;
                           }
                        try {
                           node.text("");
                           node.append("<br/>").children().selectNodes();
                           }
                        catch(e) {
                           }
                        node.removed = true;
                        }
                     this.onDeselect = function(args) {
                        if(removeOnCancel) {
                           this.displayInterface.removeMarker(this);
                           }
                        else {
                           closeDialog();
                           }
                        }
                     function dialogHandler(e) {
                        if(e.data.action == "ok" || e.data.action == "convertproblem") {
                           var question = questionEditor.getContentText();
                           var answer = answerEditor.getContentText();
                           if(me.data.questionMode != client.markers.QuestionModeConstants.annotation) {
                              if((question == "" && $("img", questionEditor.body).length == 0) || (questionDummy && questionDummy.parent().length > 0 &&!questionDummy.removed)) {
                                 alert(getQuestionDummy(me.data.questionMode, true));
                                 return;
                                 }
                              else if(question.length > MAX_TEXT_SIZE) {
                                 alert(lang.QUESTION_TOO_LONG);
                                 return}
                              else {
                                 questionEditor.fixListNesting();
                                 questionEditor.clearFormatting(utility.ClearFormattingConstants.cleanServer);
                                 question = questionEditor.getContent();
                                 if(me.data.question != question) {
                                    me.data.question = question;
                                    me.hasChanged = true;
                                    }
                                 }
                              }
                           if(answer == null || (answer == "" && $("img", answerEditor.body).length == 0) || (answerDummy && answerDummy.parent().length > 0 &&!answerDummy.removed)) {
                              if(me.data.questionMode != client.markers.QuestionModeConstants.issue || e.data.action == "convertproblem") {
                                 if(me.data.questionMode == client.markers.QuestionModeConstants.question) {
                                    me.notify = true;
                                    me.data.questionMode = client.markers.QuestionModeConstants.issue;
                                    me.hasChanged = true;
                                    me.displayInterface.triggerChange();
                                    me.data.answer = null;
                                    }
                                 else {
                                    alert(getAnswerDummy(me.data.questionMode, true));
                                    return;
                                    }
                                 }
                              else {
                                 me.data.answer = null;
                                 }
                              }
                           else if(answer.length > MAX_TEXT_SIZE) {
                              alert(lang.ANSWER_TOO_LONG);
                              return}
                           else {
                              answerEditor.fixListNesting();
                              answerEditor.clearFormatting(utility.ClearFormattingConstants.cleanServer);
                              answer = answerEditor.getContent();
                              if(me.data.answer != answer) {
                                 me.data.answer = answer;
                                 me.hasChanged = true;
                                 }
                              }
                           questionEditor.unload();
                           questionEditor = null;
                           answerEditor.unload();
                           answerEditor = null;
                           if(e.data.action == "convertproblem") {
                              me.notify = false;
                              if(me.data.questionMode != client.markers.QuestionModeConstants.question) {
                                 me.data.questionMode = client.markers.QuestionModeConstants.question;
                                 me.hasChanged = true;
                                 me.displayInterface.triggerChange();
                                 }
                              }
                           }
                        else {
                           questionEditor.unload();
                           questionEditor = null;
                           answerEditor.unload();
                           answerEditor = null;
                           if(removeOnCancel) {
                              me.displayInterface.selectMarker(null);
                              return;
                              }
                           }
                        removeOnCancel = false;
                        closeDialog();
                        updateMarker();
                        }
                     function closeDialog() {
                        if(editor !== undefined) {
                           me.startMarker.get(0).focus();
                           me.displayInterface.displayTool();
                           }
                        }
                     function dialogClosedHandler() {
                        editor = undefined;
                        if(questionEditor) {
                           if(questionEditor.body) {
                              questionEditor.body.unbind("change", dummyNodeClickHandler);
                              questionEditor.unload();
                              }
                           questionEditor = null;
                           if(answerEditor.body) {
                              answerEditor.body.unbind("change", dummyNodeClickHandler);
                              answerEditor.unload();
                              }
                           answerEditor = null;
                           }
                        questionEditor = answerEditor = questionDummy = answerDummy = null;
                        }
                     this.prepareTraining = function() {
                        if(this.trainable()) {
                           if(!this.flashCards || this.flashCards.length != 1) {
                              this.flashCards.splice(0, this.flashCards.length);
                              this.flashCards.push(new client.data.FlashCard(this));
                              }
                           var flashCard = this.flashCards[0];
                           var icon = $(document.createElement("div")).css( {
                              "background" : "url('" + config.imagePath + getIcon(this.data.questionMode, true) + "') no-repeat", "padding-left" : 24, "height" : 24, "overflow" : "hidden", "float" : "none"}
                           );
                           var text = $(document.createElement("span")).html(this.data.question).text();
                           if(text.length > 100) {
                              text = text.substr(0, 100) + "...";
                              }
                           flashCard.title = icon.append($(document.createElement("span")).text(text)).attr("title", text);
                           flashCard = null;
                           }
                        }
                     this.trainable = function() {
                        return this.data.questionMode != client.markers.QuestionModeConstants.annotation && this.data.questionMode != client.markers.QuestionModeConstants.issue;
                        }
                     function getIcon(questionMode, big) {
                        switch(questionMode) {
                           case client.markers.QuestionModeConstants.question : return"dialog-question" + (!big ? "-small" : "") + ".png";
                           case client.markers.QuestionModeConstants.annotation : return"dialog-information" + (!big ? "-small" : "") + ".png";
                           case client.markers.QuestionModeConstants.definition : return"definition" + (!big ? "-small" : "") + ".png";
                           case client.markers.QuestionModeConstants.issue : return"problem-marker" + (!big ? "-small" : "") + ".png";
                           default : return"";
                           }
                        }
                     function getTitle(questionMode) {
                        switch(questionMode) {
                           case client.markers.QuestionModeConstants.question : return lang.QUESTION;
                           case client.markers.QuestionModeConstants.annotation : return lang.ANNOTATION;
                           case client.markers.QuestionModeConstants.definition : return lang.DEFINITION;
                           case client.markers.QuestionModeConstants.issue : return lang.ISSUE;
                           default : return lang.EDITOR_TITLE;
                           }
                        }
                     this.getFlashCardContents = function(flashCard) {
                        if(flashCard != this.flashCards[0]) {
                           return null;
                           }
                        var flashCardContents = new client.data.FlashCardContents();
                        var questionHTML = this.data.question;
                        if(this.data.questionMode == client.markers.QuestionModeConstants.definition) {
                           questionHTML = "<b>" + lang.DEFINITION_QUESTION + "</b> " + questionHTML;
                           }
                        flashCardContents.frontSide = $(document.createElement("img")).attr("src", config.imagePath + getIcon(this.data.questionMode, true)).css( {
                           "float" : "right", display : "block"}
                        ).add($(document.createElement("h3")).text(getTitle(this.data.questionMode))).add($(document.createElement("hr"))).add($(document.createElement("div")).html(questionHTML));
                        flashCardContents.backSide = $(document.createElement("img")).attr("src", config.imagePath + "dialog-apply.png").css( {
                           "float" : "right", display : "block"}
                        ).add($(document.createElement("h3")).text(lang.ANSWER)).add($(document.createElement("hr"))).add($(document.createElement("div")).html(this.data.answer));
                        $("a", flashCardContents.frontSide).attr("target", "_blank");
                        $("a", flashCardContents.backSide).attr("target", "_blank");
                        flashCardContents.showSelfAssessmentButtons = true;
                        return flashCardContents;
                        };
                     this.unload = function() {
                        if(this.range) {
                           this.range.removeClass("red");
                           }
                        if(this.hasChanged) {
                           if(this.flashCards && this.flashCards.length == 1) {
                              this.flashCards[0].reset();
                              }
                           }
                        closeDialog();
                        this.startMarker = null;
                        me = null;
                        }
                     this.trashable = function() {
                        return this.data.question || this.data.answer;
                        };
                     this.createTrashMenuItem = function() {
                        var icon = getIcon(this.data.questionMode, true);
                        var label = $(document.createElement("span")).html(this.data.question || this.data.answer).text();
                        if(label.length > 15) {
                           label = label.substr(0, 15) + "...";
                           }
                        return {
                           icon : icon, label : label};
                        }
                     }
                  client.markers.QuestionMarker.prototype = new client.Marker;
                  client.markers.RepetitionMarker = function() {
                     client.Marker.call(this);
                     this.shared = false;
                     this.tool = "repetition";
                     this.createMenuItems = function(createItem) {
                        createItem(lang.REP_MARKER, "media-playlist-repeat.png", new client.markers.RepetitionMarker()).attr("accesskey", "r");
                        }
                     var me;
                     this.load = function() {
                        me = this;
                        this.startMarker = utility.createButton("media-playlist-repeat-small.png", "", lang.REP_LEVEL, null).css( {
                           width : 16, height : 16}
                        ).bind("click", this, markerClick);
                        if(this.range &&!this.displayInterface.isEditor) {
                           this.range.attr("title", lang.REP_LEVEL);
                           }
                        }
                     function markerClick(event) {
                        event.stopPropagation();
                        var me = event.data;
                        if(!me.selected) {
                           me.displayInterface.selectMarker(me);
                           showEditor();
                           }
                        else if(editor === undefined) {
                           showEditor();
                           }
                        else if(editor !== undefined) {
                           $("a.marker-button", editor).triggerHandler("click");
                           }
                        }
                     var editor;
                     function showEditor() {
                        var closeButton;
                        var panel = $(document.createElement("div"));
                        $(document.createElement("label")).attr("for", "repquestion").text(lang.QUESTION + " (" + lang.OPTIONAL + ")").appendTo(panel);
                        var questionField = $(document.createElement("textarea")).attr("id", "repquestion").css( {
                           width : "100%", height : 60}
                        ).appendTo(panel).wrap(document.createElement("div"))var shareBar = utility.createToolbar().appendTo(panel);
                        var shareCheck = $(document.createElement("input")).attr("type", "checkbox").attr("id", "sharecheck").appendTo(shareBar);
                        if(me.shared) {
                           shareCheck.attr("checked", "checked");
                           }
                        shareCheck.bind($.browser.msie ? "click" : "change", function() {
                           me.shared = this.checked; me.displayInterface.triggerChange(); me.hasChanged = true; }
                        );
                        questionField.bind("blur", shareCheck.get(0), function(event) {
                           var text = $(this).val(); if(me.data.question === undefined || me.data.question != text) {
                              event.data.checked = me.shared = (me.data.question === undefined || event.data.checked); me.data.question = text; me.displayInterface.triggerChange(); me.hasChanged = true; }
                           }
                        ).text(me.data.question !== undefined ? me.data.question : "");
                        $(document.createElement("label")).text(lang.SHARE).attr("for", "sharecheck").appendTo(shareBar);
                        var dialog = utility.createPanelDialog(panel, {
                           }
                        , undefined, lang.REPETITION);
                        dialog.toolbar.append(closeButton = utility.createMenuItem("go-previous.png", null, lang.CLOSE, closeEditor));
                        editor = me.displayInterface.displayTool( {
                           content : dialog, handler : dialogClosedHandler, clickCloses : true, offset : 230}
                        );
                        }
                     function dialogClosedHandler() {
                        editor = undefined;
                        }
                     function closeEditor() {
                        if(editor !== undefined) {
                           me.startMarker.get(0).focus();
                           me.displayInterface.displayTool();
                           }
                        }
                     this.unload = function() {
                        closeEditor();
                        this.startMarker = null;
                        me = undefined;
                        }
                     this.getFlashCardContents = function(flashCard) {
                        if(flashCard != this.flashCards[0]) {
                           return null;
                           }
                        var flashCardContents = new client.data.FlashCardContents();
                        flashCardContents.backSide = $(document.createElement("img")).attr("src", config.imagePath + "media-playlist-repeat.png").css( {
                           "float" : "right", display : "block"}
                        );
                        flashCardContents.backSide = flashCardContents.backSide.add($(document.createElement("p")).text(lang.REPEAT_TITLE));
                        if(this.data.question !== undefined) {
                           flashCardContents.backSide = flashCardContents.backSide.add($(document.createElement("h3")).text(lang.YOUR_QUESTION + ": " + this.data.question));
                           }
                        flashCardContents.backSide = flashCardContents.backSide.add($(document.createElement("p")).text(lang.REFER_FLOAT_CONTEXT));
                        flashCardContents.showCard = true;
                        flashCardContents.showSelfAssessmentButtons = true;
                        return flashCardContents;
                        };
                     this.prepareTraining = function() {
                        var flashCard = new client.data.FlashCard(this);
                        var icon = $(document.createElement("div")).css( {
                           "background" : "url('" + config.imagePath + "media-playlist-repeat.png') no-repeat", "padding-left" : 24, "height" : 24, "overflow" : "hidden", "float" : "none"}
                        );
                        var title = this.data.question !== undefined && this.data.question != "" ? this.data.question : lang.REPEAT + " " + this.card.title;
                        if(title.length > 100) {
                           title = title.substr(0, 100) + "...";
                           }
                        flashCard.title = icon.append($(document.createElement("span")).text(title)).attr("title", title);
                        this.flashCards = [flashCard];
                        flashCard = null;
                        }
                     this.trainable = function() {
                        return true;
                        }
                     }
                  client.markers.RepetitionMarker.prototype = new client.Marker;
                  client.markers.ReferenceMarker = function() {
                     client.Marker.call(this);
                     var REMOVE_IF_EMPTY = true;
                     this.shared = true;
                     this.tool = "reference";
                     this.createMenuItems = function(createItem) {
                        createItem(lang.REFERENCE, "link.png", new client.markers.ReferenceMarker()).attr("accesskey", "v");
                        }
                     var me;
                     this.load = function() {
                        me = this;
                        this.startMarker = $(document.createElement("div")).addClass("linkrange").bind("click", markerClick).bind("contextmenu", markerClick);
                        updateIcon();
                        this.range.addClass("transparent");
                        }
                     function markerClick(event) {
                        event.preventDefault();
                        event.stopPropagation();
                        if(!me.selected) {
                           me.displayInterface.selectMarker(me);
                           }
                        else if(editor === undefined) {
                           showEditor();
                           }
                        else if(editor !== undefined) {
                           $("a.marker-button", editor).triggerHandler("click");
                           }
                        }
                     this.onSelect = function() {
                        if(this.startMarker.height() <= 40) {
                           this.startMarker.css("background-image", "none");
                           }
                        showEditor();
                        }
                     var relationsTree;
                     var editor;
                     function showEditor() {
                        var urlField;
                        var closeButton;
                        var panel = $(document.createElement("div"));
                        var doQuick = false;
                        var exref;
                        if(config.reference !== undefined) {
                           if(config.reference.auto === true) {
                              if(window.parent.mbGetUrl !== undefined && window.parent.mbGetTitle !== undefined) {
                                 var url = window.parent.mbGetUrl();
                                 var title = window.parent.mbGetTitle();
                                 if(title === undefined) {
                                    title = url;
                                    }
                                 if(url !== undefined) {
                                    exref = {
                                       title : title, link : url};
                                    }
                                 }
                              }
                           else if(config.reference.link !== undefined) {
                              exref = config.reference;
                              }
                           }
                        var insertFromPlatform = exref !== undefined &&!checkExists(exref);
                        var insertFromResearch = utility.researchReference !== undefined &&!checkExists(utility.researchReference);
                        if(insertFromPlatform || insertFromResearch) {
                           var quickAddBar = utility.createToolbar().appendTo(panel);
                           var probeReference;
                           if(insertFromPlatform) {
                              probeReference = new client.data.LinkRelation(exref);
                              utility.createMenuItem(probeReference.getLinkIcon(false), null, lang.LINK_CURRENT).attr("title", lang.LINK_CURRENT_EXPLANATION).appendTo(quickAddBar).bind("click", probeReference, quickAddHandler);
                              }
                           if(insertFromResearch) {
                              probeReference = new client.data.LinkRelation(utility.researchReference);
                              var isCard = probeReference.getLinkType() == client.data.LinkRelationTypeConstants.note;
                              utility.createMenuItem(probeReference.getLinkIcon(false), null, isCard ? lang.LINK_CURRENT_NOTE : lang.LINK_CURRENT_WIKI).attr("title", isCard ? lang.LINK_CURRENT_NOTE_EXPLANATION : lang.LINK_CURRENT_WIKI_EXPLANATION).appendTo(quickAddBar).bind("click", probeReference, quickAddHandler);
                              }
                           doQuick = true;
                           }
                        $(document.createElement("h3")).text(lang.ADD_REFERENCE).appendTo(panel);
                        var addBar = utility.createToolbar().appendTo(panel);
                        $(document.createElement("span")).text(lang.TITLE).appendTo(addBar);
                        titleField = $(document.createElement("input")).attr("type", "text").appendTo(addBar);
                        $(document.createElement("span")).text(lang.LOCATION).appendTo(addBar);
                        urlField = $(document.createElement("input")).attr("type", "text").appendTo(addBar);
                        var urlObj = {
                           urlField : urlField, titleField : titleField}
                        utility.createButton("list-add.png", null, lang.INSERT, null).bind("click", urlObj, insertReferenceHandler).appendTo(addBar);
                        $(document.createElement("h3")).text(lang.REFERENCES).appendTo(panel);
                        var treeWrapper = $(document.createElement("div")).css( {
                           "max-height" : 120, "overflow-y" : "auto"}
                        ).appendTo(panel);
                        relationsTree = new client.widgets.TreeView( {
                           columns : lang.RELATIONS_LIST_HEADERS, container : treeWrapper}
                        );
                        var dialog = utility.createPanelDialog(panel, {
                           }
                        , undefined, doQuick ? lang.QUICK_REFERENCE : lang.EDIT_REFERENCE);
                        dialog.toolbar.append(closeButton = utility.createMenuItem("go-previous.png", null, lang.CLOSE, closeEditor));
                        updateList();
                        editor = me.displayInterface.displayTool( {
                           content : dialog, handler : dialogClosedHandler, offset : 130}
                        );
                        }
                     function checkExists(reference) {
                        if(typeof reference == 'object') {
                           reference = reference.link;
                           }
                        var found = false;
                        me.relations.each(function() {
                           if(this.link == reference) {
                              found = true; return false}
                           }
                        );
                        return found;
                        }
                     function dialogClosedHandler() {
                        editor = undefined;
                        if(me.relations.length == 0 && REMOVE_IF_EMPTY) {
                           me.displayInterface.removeMarker(me);
                           }
                        }
                     function quickAddHandler(event) {
                        event.stopPropagation();
                        me.relations.push(event.data);
                        me.hasChanged = true;
                        updateList();
                        updateIcon();
                        closeEditor();
                        }
                     function closeEditor() {
                        if(editor !== undefined) {
                           me.startMarker.get(0).focus();
                           me.displayInterface.displayTool();
                           }
                        }
                     this.onDeselect = function() {
                        this.startMarker.css("background-image", "");
                        closeEditor();
                        };
                     function insertReferenceHandler(event) {
                        var url = event.data.urlField.val();
                        var title = event.data.titleField.val();
                        if(url.length > 0 && title.length > 0) {
                           if(url.substr(0, 4).toLowerCase() == "www.") {
                              url = "http://" + url;
                              }
                           var args = {
                              link : url, title : (title == null || title === undefined) ? "" : title}
                           var relation = new client.data.LinkRelation(args);
                           if(!checkExists(relation)) {
                              me.relations.push(relation);
                              me.hasChanged = true;
                              updateList();
                              event.data.urlField.val("");
                              event.data.titleField.val("").get(0).focus();
                              }
                           else {
                              alert(lang.REF_EXISTS);
                              }
                           }
                        else {
                           alert(lang.ERROR_REF_EMPTY)}
                        }
                     function updateList() {
                        var items = [];
                        me.relations.each(function() {
                           if(this instanceof client.data.LinkRelation) {
                              var target; var isCard = this.getLinkType() == client.data.LinkRelationTypeConstants.note; var link = utility.createLink(this.getLinkTitle()).attr( {
                                 "href" : isCard ? "javascript:void(0)" : this.link, "target" : this.getLinkTarget(), "title" : lang.CLICK_TO_FOLLOW + " " + this.link}
                              ); if(isCard) {
                                 var cardId = this.link.substr(client.data.Card.LINK_PREFIX.length); if((cardId = parseInt(cardId)) > 0) {
                                    link.bind("click", cardId, function(event) {
                                       var cid = event.data; var topics = me.displayInterface.getTopics(); var card; topics.each(function() {
                                          this.getAllCards().each(function() {
                                             if(this.id == cid) {
                                                card = this; return false; }
                                             }
                                          )if(card !== undefined) {
                                             return false; }
                                          }
                                       ); if(card !== undefined) {
                                          me.displayInterface.openCard(card); }
                                       }
                                    ); }
                                 }
                              var remover = utility.createLink(lang.REMOVE).bind("click", this, removeReferenceHandler); var item = new client.widgets.TreeViewItem(link, [remover], [], this.getLinkIcon()); items.push(item); }
                           }
                        );
                        relationsTree.unload();
                        relationsTree.load( {
                           items : items}
                        );
                        }
                     function removeReferenceHandler(event) {
                        me.relations.remove(event.data);
                        me.hasChanged = true;
                        updateList();
                        }
                     function updateIcon() {
                        var title = lang.REFERENCE;
                        if(me.relations.length > 0) {
                           title = me.relations[0].title;
                           }
                        me.startMarker.attr("title", title);
                        }
                     this.unload = function() {
                        closeEditor();
                        this.startMarker = null;
                        me = null;
                        }
                     this.update = function(args) {
                        args.handled = true;
                        outerElements = this.range.getFirstAndLast();
                        var relation;
                        relation = outerElements.first;
                        firstPos = utility.getRelativePosition(relation, relation.ownerDocument.body);
                        relation = outerElements.last;
                        lastPos = utility.getRelativePosition(relation, relation.ownerDocument.body);
                        var pos = {
                           top : firstPos.top - 2, height : lastPos.top - firstPos.top + relation.offsetHeight + 4};
                        if(pos.top < 0) {
                           pos.top = 0;
                           }
                        if(pos.height < 0) {
                           pos.height = 6;
                           }
                        this.startMarker.css(pos);
                        }
                     this.trashable = function() {
                        return this.relations.length > 0;
                        }
                     this.createTrashMenuItem = function() {
                        var title = lang.REFERENCE;
                        if(this.relations.length > 0) {
                           title = this.relations[0].title;
                           }
                        return {
                           icon : "link.png", label : title};
                        }
                     }
                     client.markers.ReferenceMarker.prototype=new client.Marker;