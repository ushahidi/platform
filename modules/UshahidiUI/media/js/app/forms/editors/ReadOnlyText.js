define(['backbone', 'backbone-forms',],
	function(Backbone)
{
	var ReadOnlyText = Backbone.Form.editors.ReadOnlyText = Backbone.Form.editors.Base.extend({
		tagName : 'div',

		render: function() {
			this.setValue(this.value);

			return this;
		},

		getValue: function() {
				return this.$el.text();
		},

		setValue: function(value) {
				this.$el.text(value);
		},


	});
	return ReadOnlyText;
});