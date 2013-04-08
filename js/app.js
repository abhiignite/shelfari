
<script>

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

	var Books = Backbone.Collection.extend({
      url: 'server/books'
    });

	var Book = Backbone.Model.extend({
      urlRoot: 'server/books'
    });

	var Book2 = Backbone.Model.extend({
      urlRoot: 'server/books/search'
    });	
	
	var Books2 = Backbone.Collection.extend({
		model:Book2,
		url:'server/books/search'
		});
	
	
	var BooksList = Backbone.View.extend({
      el: '.page' ,
	  
      render: function () {
		var that =this;
		var books = new Books();
		books.fetch({
			success: function(books){
			console.log(JSON.stringify(books));
		var template = _.template($('#user-list-template').html(), {books: books.models});
		console.log("fetching");
		that.$el.html(template);
		} });	
      }
    });
	
	
	var SearchList = Backbone.View.extend({
      el: '.page',
	  
	  events: {
	  'blur .see' : 'searchUser'
	  },
	  
	  searchUser: function(evt) {
		var query = $('.see').val();
		console.log('search --> '+query);
		router.navigate('#/search/'+query, {trigger : true});
			return false;
		},
	  
      render: function (options) {
		var that =this;
		
		if(options.name) {
			
			that.books = new Book2({id:options.name});
			that.books.fetch({
			success: function(books){
				console.log("found books");
			
				var template = _.template($('#user-list-template').html(), {books:books.model});
				that.$el.html(template);
				console.log(JSON.stringify(books));
				}
			});
			}	
      }
    });
	
	
	
	
	var EditBook = Backbone.View.extend({
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
		'submit .edit-user-form': 'saveUser',
		'click .delete': 'deleteUser'
		
		},
		saveUser: function(evt) {
			var bookDetails = $(evt.currentTarget).serializeObject();
			var book = new Book();
			book.save(bookDetails , {
			success: function(book) {
				router.navigate('', {trigger:true});
				}
			});	
			return false;
		},
		
		deleteUser: function(evt) {
		
			this.book.destroy({
				success: function() {
					router.navigate('', {trigger:true});
				}
			})
				return false;
			 }
			
		});

	var bookslist = new BooksList();
	var editBook = new EditBook();
	var searchList = new SearchList();
	
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
			console.log('We have loaded the page ');
		});
		
		router.on('route:editBook', function(id){
		editBook.render({id:id});
		console.log('called edit');
		});
		
		router.on('route:findbyName' , function(name) {
		console.log('append ---> '+name);
		searchList.render({name:name});
		});
		
		Backbone.history.start();
</script>
