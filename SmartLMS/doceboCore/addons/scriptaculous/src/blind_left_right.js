
Effect.BlindLeftAndRight = function(element) {
	
	if(Element.visible(element)) new Effect.BlindOutLat(element);
	else new Effect.BlindInLat(element);
}

/* need a subelement that contain all the element of the box to blind */

Effect.BlindOutLat = function(element) {
  element = $(element);
  
  var dim = element.getDimensions();
  element.makeClipping();
  element.firstChild.style.position = 'relative';
  element.firstChild.style.width = dim.width+'px';
  
  return new Effect.Scale(element, 0,
    Object.extend({ scaleContent: false, 
      scaleY: false,                     
      restoreAfterFinish: true,
      afterFinishInternal: function(effect) {
        effect.element.hide().undoClipping();
      } 
    }, arguments[1] || {})
  );
}

Effect.BlindInLat = function(element) {
  element = $(element);
  var elementDimensions = element.getDimensions();
  
  element.firstChild.style.position = 'relative';
  element.firstChild.style.width = elementDimensions.width+'px';
  
  return new Effect.Scale(element, 100, Object.extend({ 
    scaleContent: false, 
    scaleY: false,
    scaleFrom: 0,
    scaleMode: {originalHeight: elementDimensions.height, originalWidth: elementDimensions.width},
    restoreAfterFinish: true,
    afterSetup: function(effect) {
      effect.element.makeClipping().setStyle({width: '0px'}).show(); 
    },  
    afterFinishInternal: function(effect) {
      effect.element.undoClipping();
    }
  }, arguments[1] || {}));
}
