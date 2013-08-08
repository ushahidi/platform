define(['marionette', 'controllers/Controller'],
	function(Marionette, Controller) {
		return Marionette.AppRouter.extend(
		{
			appRoutes :
			{
				"" : "index",
				"views/list" : "viewsList",
				"views/map" : "viewsMap",
				"posts/:id" : "postDetail",
				"*path" : "index"
			}
		});
	}); 
