/**
 * Based on handlebars-paginate https://github.com/olalonde/handlebars-paginate
 * License unknown
 * TODO: check license perms
 */

define([], function() {
	var paginate = function(pagination, options) {
		var type = options.hash.type || 'middle';
		var ret = '';
		var pageCount = pagination.totalPages;
		var currentPage = pagination.currentPage +1;
		var pageSize = pagination.pageSize;
		var totalRecords = pagination.totalRecords;
		var limit;
		if (options.hash.limit)
			limit = +options.hash.limit;

		//page pageCount
		var newContext =
		{
		};
		switch (type)
		{
			case 'middle':
				if ( typeof limit === 'number')
				{
					var i = 0;
					var leftCount = Math.ceil(limit / 2) - 1;
					var rightCount = limit - leftCount - 1;
					if (currentPage + rightCount > pageCount)
						leftCount = limit - (pageCount - currentPage) - 1;
					if (currentPage - leftCount < 1)
						leftCount = currentPage - 1;
					var start = currentPage - leftCount;

					while (i < limit && i < pageCount)
					{
						newContext =
						{
							n : start
						};
						if (start === currentPage)
							newContext.active = true;
						ret = ret + options.fn(newContext);
						start++;
						i++;
					}
				}
				else
				{
					for (var i = 1; i <= pageCount; i++)
					{
						newContext =
						{
							n : i
						};
						if (i === currentPage)
							newContext.active = true;
						ret = ret + options.fn(newContext);
					}
				}
				break;
			case 'previous':
				if (currentPage === 1)
				{
					newContext =
					{
						disabled : true,
						n : 1
					}
				}
				else
				{
					newContext =
					{
						n : currentPage - 1
					}
				}
				ret = ret + options.fn(newContext);
				break;
			case 'next':
				newContext =
				{
				};
				if (currentPage === pageCount)
				{
					newContext =
					{
						disabled : true,
						n : pageCount
					}
				}
				else
				{
					newContext =
					{
						n : currentPage + 1
					}
				}
				ret = ret + options.fn(newContext);
				break;
			case 'info':
				// Keep current pagination state values
				newContext = pagination;
				// First record of current page
				newContext.startRecord = ((currentPage-1) * pageSize) +1;
				// Last record of current page
				newContext.endRecord = currentPage * pageSize;
				newContext.endRecord > totalRecords ? newContext.endRecord = totalRecords : null;
				
				ret = ret + options.fn(newContext);
				break;
		}

		return ret;
	};
	return paginate;
});
