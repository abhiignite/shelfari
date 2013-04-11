<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Shelfari</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
<script src="lib/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="lib/underscore-min.js" type="text/javascript"></script>
<script src="lib/backbone-min.js"></script>


<div class="navbar navbar-inverse navbar-fixed-top" xmlns="http://www.w3.org/1999/html">
    <div class="navbar-inner">
        <div class="container">
			
            <a class="brand" href="#">Shelfari</a>
			

            <div class="nav-collapse">
                <ul class="nav">
				<a href="#/new" class="btn btn-small btn-primary">Add Book</a>
			    </ul>
			</div>
		</div>
	</div>
</div>

<div class="container">

<div class="page"></div>

</div>



<script type="text/template" id="book-list-template">
	
		<form class="navbar-search pull-right" id="search">
			<input class="search-query" name="searchText" type="text" id="searchText" placeholder="Search books"/>
			<button type="submit" class="btn">Submit</button>
		</form>
	


	<div class="text">
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th>Name</th><th>Author</th><th>Status</th><th></th>
				</tr>
			</thead>
			<tbody>
				<% _.each(books, function(book) { %>
					<tr>
						<td><%= book.get('name') %></td>
						<td><%= book.get('author') %></td>
						<td><%= book.get('status') %></td>
						<td><a class="btn" href="#/edit/<%= book.id %>">Edit</a></td>		
					</tr>
				<% }); %>
			</tbody>
		</table>
	</div>
</script>


<script type="text/template" id="edit-book-template">
    <form class="edit-book-form">
		<legend><%= book ? 'Edit' : 'New' %> Book</legend>
        <label>Name</label>
        <input name="name" type="text" value="<%= book ? book.get('name') : '' %>" />
        <label>Author</label>
        <input name="author" type="text" value="<%= book ? book.get('author') : '' %>" />
        <label>Status</label>
		<select name="status">
		<option>Unread</option>
		<option>Read</option>
		</select>
        <hr />
		<button type="submit" class="btn"><%= book ? 'Update' : 'Create' %></button>
		<% if(book) { %>
        <input type="hidden" name="id" value="<%= book.id %>" />
		<button data-user-id="<%= book.id %>" class="btn btn-danger delete">Delete</button>
       <% }; %>
	   
    </form>
  </script>





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

	
	/*   Collections */
	var Books = Backbone.Collection.extend({
      url: 'server/books'
    });
	
	var Books2 = Backbone.Collection.extend({
		url:'server/books/search'
		});

	/* Models   */
	var Book = Backbone.Model.extend({
      urlRoot: 'server/books'
    });
	
	
	/* Views */
	
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
					console.log(JSON.stringify(books));
					var template = _.template($('#book-list-template').html(), {books: books.models});
					that.$el.html(template);
				} 
			});	
		}
    });
	
	
	var SearchList = Backbone.View.extend({
		getTitle: function (options) {
			if(options.name)
				return "Books Search"; //Can add pattern to be searched here.
			else
				return "Search book";
		},
		el: '.page',
		events: {
			'submit #search' : 'searchUser'
		},
	  
		searchUser: function(evt) {
			var query = $('#searchText').val();
			console.log('search --> '+query);
			router.navigate('#/search/'+query, {trigger : true});
			return false;
		},
	  
		render: function (options) {
			
			var that =this;
			var books = new Books2();
			
			books.fetch({
				data : {name: options.name
				},
				success: function(books){
					console.log("fetched the searched query");
					console.log(JSON.stringify(books));
					var template = _.template($('#book-list-template').html(), {books:books.models});
					that.$el.html(template);
					
				}
				
			
			});
					
						
		}
    });
	
	
	
	var EditBook = Backbone.View.extend({
		getTitle: function (options) {
			if(options.id)
				return "Edit Book "+options.id;
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
						var template = _.template($('#edit-book-template').html(), {book:book});
						console.log('editing');
						that.$el.html(template);
					}
				});
			}
			else {
				var template = _.template($('#edit-book-template').html(), {book:null });
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
			this.book.destroy({
				success: function() {
					window.history.back();
				}
			})
				return false;
			 }
			
		});

		
	var bookslist = new BooksList();
	var editBook = new EditBook();
	var searchList = new SearchList();
	
	
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
		console.log('Page Loaded');
	});
		
	router.on('route:editBook', function(id){
		editBook.render({id:id});
		console.log('called edit');
		$(document).attr('title', editBook.getTitle({id:id}));
	});
		
	router.on('route:findbyName' , function(name) {
		console.log('Query ---> '+name);
		searchList.render({name:name});
		$(document).attr('title', searchList.getTitle({name:name}));
	});
		
	Backbone.history.start();
	
	
</script>


