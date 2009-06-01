Object.extend(Event, {
  observe: function(element, name, observer, useCapture) {
    var element = $(element);
    useCapture = useCapture || false;
    if (name == 'keypress' &&             (navigator.appVersion.match(/Konqueror|Safari|KHTML/) || 
element.attachEvent))
      name = 'keydown';
    if (name == 'load' && element.screen)
      this._observeLoad(element, name, observer, useCapture);
    else
      this._observeAndCache(element, name, observer, useCapture);
  },
  _observeLoad : function(element, name, observer, useCapture) {
    if (!this._readyCallbacks) {
      var loader = this._onloadWindow.bind(this);
      if (document.addEventListener)
          document.addEventListener("DOMContentLoaded", loader, false);
      /*@cc_on @*/
      /*@if (@_win32)
	if (!$("__ie_onload")) {
          document.write("<script id='__ie_onload' defer='true' src='://'><\/script>");
          var script = $("__ie_onload");
          script.onreadystatechange = function() { if (this.readyState == "complete") loader(); };
      } else {
        loader();
      }
      /*@end @*/
      if (navigator.appVersion.match(/Konqueror|Safari|KHTML/i))
        Event._timer = setInterval(function() {if 
(/loaded|complete/.test(document.readyState))loader();}, 10);
      Event._readyCallbacks =  [];
      this._observeAndCache(element, name, loader, useCapture);
    }
    Event._readyCallbacks.push(observer);
  },
  _onloadWindow : function() {
    if (arguments.callee.done) return;
    arguments.callee.done = true;
    if (this._timer) clearInterval(this._timer);
    this._readyCallbacks.each(function(f) { f() });
    this._readyCallbacks = null;
  }
});