var app = new Vue({
	el: '#app',
	data: {
		login: '',
		pass: '',
		post: false,
		postId: false,
		invalidLogin: false,
		invalidPass: false,
		invalidSum: false,
		posts: [],
		addSum: '10.00',
		amount: 0,
		likes: 0,
		commentText: '',
		packs: [
			{
				id: 1,
				price: 5
			},
			{
				id: 2,
				price: 20
			},
			{
				id: 3,
				price: 50
			},
		],
	},
	computed: {
		test: function () {
			var data = [];
			return data;
		}
	},
	created(){
		var self = this;
		axios
			.get('/main_page/get_all_posts')
			.then(function (response) {
				self.posts = response.data.posts;
			})
	},
	methods: {
		logout: function () {
			console.log ('logout');
		},
		logIn: function () {
			var self= this;
			if(self.email === ''){
				self.invalidLogin = true
			}
			else if(self.pass === ''){
				self.invalidLogin = false
				self.invalidPass = true
			}
			else{
				self.invalidLogin = false
				self.invalidPass = false

				const form = new FormData();

				form.append('email', self.login);
				form.append('password', self.pass);

				axios.post('/main_page/login', form)
					.then(function (response) {
						setTimeout(function () {
							$('#loginModal').modal('hide');
						}, 500);
					})
			}
		},
		fiilIn: function () {
			var self= this;
			if(self.addSum === 0){
				self.invalidSum = true
			}
			else{
				self.invalidSum = false

				const form = new FormData();
				form.append('sum', self.addSum);

				axios.post('/main_page/add_money', form)
					.then(function (response) {
						setTimeout(function () {
							if(response.data.status === 'success') {
								$("#userBalance").text(response.data.balance);
								$('#addModal').modal('hide');
							} else {
								self.invalidSum = true;
								self.sumError = response.data.sum;
							}
						}, 500);
					})
			}
		},
		openPost: function (id) {
			var self= this;
			axios
				.get('/main_page/get_post/' + id)
				.then(function (response) {
					self.post = response.data.post;
					if(self.post){
						setTimeout(function () {
							$('#postModal').modal('show');
						}, 500);
					}
				})
		},
		addLike: function (id) {
			var self= this;
			axios
				.get('/main_page/like/'+id)
				.then(function (response) {
					self.likes = response.data.likes;
				})

		},
		buyPack: function (id) {
			var self= this;
			axios.post('/main_page/buy_boosterpack', {
				id: id,
			})
				.then(function (response) {
					self.amount = response.data.amount
					if(self.amount !== 0){
						setTimeout(function () {
							$('#amountModal').modal('show');
						}, 500);
					}
				})
		},
		comment: function () {
			var self = this;

			const form = new FormData();

			form.append('post_id', self.post.id);
			form.append('message', self.commentText);

			axios.post('/main_page/comment', form)
				.then(function (response) {
					setTimeout(function () {
						if(response.data.status === 'success') {
							self.commentText = '';
							// realtime update
							self.post.coments = self.post.coments.concat(response.data.comment);
							console.log(self.post.coments);
						}
					}, 500);
				});
		}
	}
});

