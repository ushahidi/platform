define([ 'marionette', 'handlebars', 'text!templates/modals/EditPost.html'],
	function( Marionette, Handlebars, template)
	{
		return Marionette.ItemView.extend( {
			template: Handlebars.compile(template),
			initialize: function() { },
			className: 'edit-post',
			events : {
				'click .post-title' : 'editPostTitle',
				'click .post-excerpt' : 'editPostContent'
			},
			editPostTitle : function(e)
			{
				var $el = this.$(e.currentTarget),
					$input,
					text;
				
				text = $el.text();
				$el.text('');
				$input = Marionette.$('<input />').val(text);
				
				$input.appendTo($el).select();
				$input.blur(
					function()
					{
						var newText = $input.val();
						$el.empty.text(newText);
					}
				);
			},
			editPostContent : function(e)
			{
				var $el = this.$(e.currentTarget),
					$input,
					text;
				
				text = $el.text();
				$el.text('');
				$input = Marionette.$('<textarea />').val(text);
				
				$input.appendTo($el).select();
				$input.blur(
					function()
					{
						var newText = $input.val();
						$el.empty.text(newText);
					}
				);
			}
		});
	});
