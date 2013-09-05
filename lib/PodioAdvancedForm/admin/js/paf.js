$(function(){
	var Element = Backbone.Model.extend({
		defaults: {
			parent: false,
			hidden: false,
			locked: false,
			value: ''
		}
	});
	
	var ElementView = Backbone.View.extend({
		initialize: function(){
			this.listenTo(this.model, 'change:hidden', this.toggleHidden);
			this.listenTo(this.model, 'change:locked', this.toggleLocked);
			// add lock and hide buttons
			var html = '';
			if (this.$el.hasClass('control-label')){
				html = '<br>';
			}
			html += ' <a href="#" class="btn btn-mini" data-locked="false" data-label-active="Locked" data-label-inactive="Lock">Lock</a> ';
			html += '<a href="#" class="btn btn-mini" data-hidden="false" data-label-active="Hidden" data-label-inactive="Hide">Hide</a>'
			
			var hidden =  $('[name="' + this.model.get('name') + '"]').attr("hidden");
			var locked =  $('[name="' + this.model.get('name') + '"]').attr("locked");
			
			if (typeof hidden !== 'undefined' && hidden !== false) {
				this.model.set('hidden', true)
			}
			
			if (typeof locked !== 'undefined' && locked !== false) {
				this.model.set('hidden', true)
			}
			
			this.$el.append(html);
		},
		events: {
			"mouseenter": "hover",
			"mouseleave": "hover",
			"click a[data-hidden]": "clickHidden",
			"click a[data-locked]": "clickLocked",
		},
		hover: function(){
			this.$el.next().toggleClass('hover');
		},
		click: function(attr){
			this.model.set(attr, !this.model.get(attr));
			
			console.log(this.model.get('parent'));	
			
//			if (this.model.get('parent')){
//				var name = this.model.get('name');
//				var elements = this.collection.filter(function(item){
//					if (item.model.get('name').indexOf(name) === 0){
//						return true;
//					}
//				});
//				
//				console.log(elements);
//			}
		},
		clickHidden: function(ev){
			ev.preventDefault();
			this.click('hidden');
		},
		clickLocked: function(ev){
			ev.preventDefault();
			this.click('locked');
		},
		toggle: function(attr){
			$el = this.$el.find('[data-' +attr+ ']');
			if (this.model.get(attr)){
				$el.addClass('active').text($el.data('label-active'));
			} else {
				$el.removeClass('active').text($el.data('label-inactive'));
			}
		},
		toggleHidden: function(){
			console.log('toggle hidden');
			this.toggle('hidden');
		},
		toggleLocked: function(){
			console.log('toggle locked');
			this.toggle('locked');
		},
		
	});
	
	var Form = Backbone.Collection.extend({
		model: Element,
	});
	
	var FormView = Backbone.View.extend({
		el: '.podio-advanced-form',
		initialize: function(){
			var view = this;
			this.collection = new Form;
			this.listenTo(this.collection, 'add', this.add);
			this.$el.find('label[for]').each(function(i,el){
				var name = $(el).attr('for');
				//console.log(name);
				if (name.indexOf('[') !== -1){
					var parent = name.substring(0,name.indexOf('['));
					console.log(parent);
					if (!view.collection.findWhere({name: parent})){
						var model = new Element({
							name: parent,
							parent: true
						});
						
						var parent_el = $(el).closest('fieldset').find('legend');
						
						new ElementView({
							el: parent_el,
							model: model
						});
						
						view.collection.add(model);
					}
				}
				
				// kolla om el innehåller ett fieldset
				// isf sätt parent = true
				
				var model = new Element({
					name: name
				});

				new ElementView({
					el: $(el),
					model: model
				});

				view.collection.add(model);
			});
		},
		add: function(el){
			//console.log(el);
		}
	});
	
	new FormView;
});
