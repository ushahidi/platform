/**
 * Notify utils
 *
 * @module     utils/notify
 * @author     Ushahidi Team <team@ushahidi.com>
 * @copyright  2014 Ushahidi
 * @license    https://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License Version 3 (AGPL3)
 */

define(['alertify', 'jquery', 'i18next', 'underscore'],
	function(alertify, $, i18n, _)
	{
		var
		translate = function (type, action, status, default_action, context) {
			return i18n.t(
					[
						'notify.'+type+'.'+action+'_'+status,
						'notify.'+type+'.'+default_action+'_'+status,
						'notify.default.'+action+'_'+status,
						'notify.default.'+default_action+'_'+status
					],
					context
			);
		},
		save = function (model, type, action) {
			var promise = model.save();

			type = type || 'default';
			action = action || 'save';

			promise.done(function()
				{
					alertify.success(translate(type, action, 'success', 'save'));
				})
				.fail(function (xhr)
				{
					alertify.error(translate(type, action, 'error', 'save'));
				});

			return promise;
		},
		destroy = function (model, type, action) {
			var dfd = $.Deferred();

			type = type || 'default';
			action = action || 'destroy';

			alertify.confirm(translate(type, action, 'confirm', 'destroy'), function(confirm)
			{
				if (confirm)
				{
					var collection = model.collection;

					model
						.destroy({
							// Wait till server responds before destroying model
							wait: true
						})
						.done(function()
						{
							dfd.resolve(arguments);
							alertify.success(translate(type, action, 'success', 'destroy'));
							// Trigger a fetch. This is to remove the model from the listing and load another
							if (collection)
							{
								collection.fetch();
							}
						})
						.fail(function ()
						{
							dfd.reject(arguments);
							alertify.error(translate(type, action, 'error', 'destroy'));
						});
				}
				else
				{
					alertify.log(translate(type, action, 'cancelled', 'destroy'));
					dfd.reject();
				}
			});

			return dfd.promise();
		},
		bulkDestroy = function (selected, type, action) {
			var dfd = $.Deferred();

			type = type || 'default';
			action = action || 'bulk_destroy';

			alertify.confirm(translate(type, action, 'confirm', 'bulk_destroy', { count: selected.length }), function(e)
			{
				var responses = [],
					collection = selected[0].model.collection; // assuming all models in the same collection

				if (e)
				{
					_.each(selected, function(item) {
						var model = item.model,
							response = model.destroy({wait : true});

						response.done(function()
							{
								alertify.success(translate(type, action, 'success', 'destroy', { id : model.id }));
							})
							.fail(function ()
							{
								alertify.error(translate(type, action, 'error', 'destroy', { id : model.id }));
							});

						responses.push(response);
					});

					// Reload the collection when all requests are done
					$.when.apply($, responses).done(function () {
						if (collection)
						{
							collection.fetch();
						}
					}).always(function() {
						var args = Array.prototype.slice.call(arguments),
							failures = _.filter(args, function(a) {
								return (a[1] !== 'success');
							});

						failures.length ? dfd.reject() : dfd.resolve();
					});
				}
				else
				{
					alertify.log(translate(type, action, 'cancelled', 'destroy'));
					dfd.reject();
				}
			});

			return dfd.promise();
		};

		return {
			save : save,
			destroy: destroy,
			bulkDestroy : bulkDestroy
		};
	}
);
