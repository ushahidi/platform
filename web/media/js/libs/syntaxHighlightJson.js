/**
 * Using code from here
 *
 * http://stackoverflow.com/questions/4810841/how-can-i-pretty-print-json-using-javascript
 * See  comment by Pumbaa80
 */

(function($) {
	var syntaxHighlight = function(json) {
		if (typeof json != 'string') {
			json = JSON.stringify(json, undefined, 2);
		}
		json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
		return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
				var cls = 'number';
				if (/^"/.test(match)) {
						if (/:$/.test(match)) {
								cls = 'key';
						} else {
								cls = 'string';
						}
				} else if (/true|false/.test(match)) {
						cls = 'boolean';
				} else if (/null/.test(match)) {
						cls = 'null';
				}
				return'<span class="' + cls + '">' + match + '</span>';
		});
	};

	$.fn.syntaxHighlightJson = function(jsonData) {
		this.html(syntaxHighlight(jsonData));
	};
})(jQuery);