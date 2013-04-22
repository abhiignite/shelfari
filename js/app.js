(function($) {

$.fn.serializeObject = function() {
      var o = {};
      var a = this.serializeArray();
      $.each(a, function() {
          if (o[this.name] !== undefined) {
              if (!o[this.name].push) {
                  o[this.name] = [o[this.name]];
              }
              o[this.name].push(this.value || '');
          } else {
              o[this.name] = this.value || '';
          }
      });
      return o;
    };

	
	/*   Collections */
	var Books = Backbone.Collection.extend({
      url: 'server/books'
    });
	
	var Books2 = Backbone.Collection.extend({
		url:'server/books/search'
		});

	/* Models   */
	var Book = Backbone.Model.extend({
    urlRoot: 'server/books',
    });
 
	
	
	/* Views */

	var SearchBoxView = Backbone.View.extend({
		el:'.head',
		initialize: function() {
		
	},
		events: {
			'keyup #searchText' : 'searchUser'
		},
	  
		searchUser: function(evt) {
			var self = this;
			if(self.timer)
				clearTimeout(self.timer);
				self.timer = setTimeout(function() {
				console.log('fired');
				var query = $('#searchText').val();
			console.log('search --> '+query);
			if(query.length>0) {
				router.navigate('#/search/'+query, {trigger : false});
			}
			else {
				router.navigate('#', {trigger:false});
			}
			var searchlist = new SearchList();
			searchlist.render({name:query})
			return false; 
			self.timer = null;
				}, 300);
		}
			
	});
	
	
	
	var BooksList = Backbone.View.extend({
		getTitle: function () {
        return "Shelfari home";
    },
		el: '.page' ,
		render: function () {
			var that =this;
			var books = new Books();
			books.fetch({
				success: function(books){
					var template = _.template($('#book-list-template').html(), {books: books.models});
					that.$el.html(template);
				} 
			});	
		}
    });
	
	
	var SearchList = Backbone.View.extend({
		getTitle: function (options) {
			if(options.name)
				return "Search book";
			
		},
		el: '.page',
		
		render: function (options) {
			
			var that =this;
			var books = new Books2();
			
			books.fetch({
				data : {name: options.name
				},
				success: function(books){
					var template = _.template($('#book-list-template').html(), {books:books.models});
					that.$el.html(template);
					
				}
				
			
			});
					
						
		}
    });
	
	
	
	var EditBook = Backbone.View.extend({
		getTitle: function (options) {
			if(options.id)
				return "Edit Book"+options.id;
			else
				return "Add book";
		},
		el: '.page' ,
		render: function(options) {
			var that = this;
			if(options.id) {
				that.book = new Book({id:options.id});
				that.book.fetch({
					success: function(book) {
						console.log(JSON.stringify(book));
						var template = _.template($('#edit-user-template').html(), {book:book});
						console.log('editing');
						that.$el.html(template);
					}
				});
			}
			else {
				var template = _.template($('#edit-user-template').html(), {book:null });
				console.log('editing');
				this.$el.html(template);
				}
		},
		
		events :{
			'submit .edit-book-form': 'saveBook',
			'click .delete': 'deleteBook'
		},
		
		saveBook: function(evt) {
			var bookDetails = $(evt.currentTarget).serializeObject();
			var book = new Book();
			book.save(bookDetails , {
			success: function(book) {
				window.history.back();
				}
			});	
			return false;
		},
		
		deleteBook: function(evt) {
		this.book.destroy();  			
			 }
			
		});

	var bookslist = new BooksList();
	var editBook = new EditBook();
	var searchList = new SearchList();
	var searchview = new SearchBoxView();
	
	/* Routers */
	
	
	var Router = Backbone.Router.extend({
		routes: {
			'':'home',
			'new':'editBook',
			'edit/:id':'editBook',
			'search/:name' : 'findbyName'
		}
	});
		
	
	var router = new Router();
	
	
	router.on('route:home', function(){
		bookslist.render();
		$(document).attr('title', bookslist.getTitle);
		console.log('We have loaded the page ');
	});
		
	router.on('route:editBook', function(id){
		editBook.render({id:id});
		console.log('called edit');
		$(document).attr('title', editBook.getTitle({id:id}));
	});
			
	router.on('route:findbyName' , function(name) {
		searchList.render({name:name});
		$(document).attr('title', searchList.getTitle({name:name}));
	});
	
	Backbone.history.start();
	
	
})(jQuery);

