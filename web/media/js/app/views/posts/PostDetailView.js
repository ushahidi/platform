/**
 * Post Detail
 *
 * @module     PostDetailView
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2013 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['underscore', 'jquery', 'App', 'views/posts/PostItemView', 'hbs!templates/posts/PostDetail', 'hbs!templates/values/ValueContainer'],
	function(_, $, App, PostItemView, template, valueContainerTemplate)
	{
		var PostDetailView = PostItemView.extend(
		{
			//Template HTML string
			template: template,

			modelEvents: {
				'sync': 'render',
				'destroy' : 'handleDeleted'
			},

			events : _.extend(PostItemView.prototype.events, {
				'click .js-post-next' : 'showNextPost',
				'click .js-post-prev' : 'showPrevPost'
			}),

			handleDeleted : function()
			{
				// Redirect user to previous page (probably post list)
				// @todo does this always make sense?
				window.history.back();
			},

			onDomRefresh : function()
			{
				var $valuesEl = this.$('.post-values'),
					data = this.model.toJSON(),
					i;

				for (i in data.values)
				{
					this.renderField(i, data.values[i], $valuesEl);
				}
			},

			renderField : function(key, value, $el)
			{
				var
					form = App.Collections.Forms.get(this.model.get('form')),
					attribute = form.getAttribute(key),
					i,
					$fieldEl;

				$fieldEl = $(valueContainerTemplate({
					label : (typeof attribute.label !== 'undefined') ? attribute.label : key,
					key : key
				}));
				$el.append($fieldEl);

				if (_.isArray(value))
				{
					for (i = 0; i < value.length; i++)
					{
						this.renderFieldValue(attribute, key, value[i], $fieldEl);
					}
				}
				else
				{
					this.renderFieldValue(attribute, key, {value: value, id: null}, $fieldEl);
				}
			},
			//@todo move elsewhere
			attributeTypeViewMap : {
				'datetime' : 'DateTimeValue',
				'link' : 'LinkValue',
				'point' : 'PointValue',
				'geometry' : 'GeometryValue',
			},

			renderFieldValue : function (attribute, key, value, $el)
			{
				var viewName = (typeof this.attributeTypeViewMap[attribute.type] !== 'undefined') ?
					this.attributeTypeViewMap[attribute.type] :
					'Value';

				require(['views/values/' + viewName], function (ValueView)
				{
					var view = new ValueView({
						attribute : attribute,
						key : key,
						value : value.value,
						value_id : value.id
					});
					view.render();
					$el.append(view.el);
				});
			},

			showNextPost : function()
			{
				var collection = this.collection,
					next = this.model.getNext(collection);

				if (! collection)
				{
					ddt.log('PostDetailView', 'Post has no collection');
					return false;
				}

				if (next)
				{
					App.appRouter.navigate('posts/' + next.id, { trigger: true });
				}
				else
				{
					collection.getNextPage()
						.done(function()
							{
								ddt.log('PostDetailView', 'collection after next paging', collection);
								next = collection.at(0);
								if (next)
								{
									App.appRouter.navigate('posts/' + next.id, { trigger: true });
								}
							});
				}
			},

			showPrevPost : function()
			{
				var collection = this.collection,
					prev = this.model.getPrev(collection);

				if (! collection)
				{
					ddt.log('PostDetailView', 'Post has no collection');
					return false;
				}

				if (prev)
				{
					App.appRouter.navigate('posts/' + prev.id, { trigger: true });
				}
				else
				{
					collection.getPreviousPage()
						.done(function()
							{
								ddt.log('PostDetailView', 'collection after prev paging', collection);
								prev = collection.at(collection.length);
								if (prev)
								{
									App.appRouter.navigate('posts/' + prev.id, { trigger: true });
								}
							});
				}
			}

		});

		return PostDetailView;
	});
