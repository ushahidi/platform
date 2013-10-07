define(['marionette'],
	function(Marionette) {
		return Marionette.AppRouter.extend(
		{
			appRoutes :
			{
				'' : 'index',
				'views/list' : 'viewsList',
				'views/map' : 'viewsMap',
				'posts/create' : 'postCreate',
				'posts/:id' : 'postDetail',
				'sets' : 'sets',
				'login' : 'login',
				'register' : 'register',
				'*path' : 'index'
			}
		});
	});