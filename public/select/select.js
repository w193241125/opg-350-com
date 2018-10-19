;(function($){
	var SelectForGame = function(obj) {
		var self = this;
		this.isShow = false;
		this.obj = obj;
		this.option = obj.find('.option');
		this.titleBtn = obj.find('.title');
		this.box = obj.find('input:checkbox');
		this.lastTitle = obj.find('.last-title');
		this.topTitle = obj.find('.top-title');
		this.searchArea = obj.find('.search-area');
		this.firstCon = obj.find('.first-con');
		this.selectDown = $('.select-down');
		this.searchResult = obj.find('.search-result');
		this.topTitleText = '';
		// this.searchLi = undefined;
		this.allText = [];
		this.getlastTextArray();
		// this.optionText = obj.find('.first-con li').first().text();
		this.searchArea.focus(function(e) {
			console.log(2222)
			self.topTitleText = '';
			self.searchArea.attr('value','');
			self.searchResult.hide();
			self.firstCon.show();
		});
        this.lastTitle.each(function(index, item) {
            if($(item).attr('checked') == 'checked') {
                self.setChecked($(item))
            }
            self.getCheckText();
            self.searchArea.attr('value', self.topTitleText);
        });
		$(document).click(function(){
			self.getCheckText();
			self.searchArea.attr('value', self.topTitleText);
			self.firstCon.hide();
			self.searchResult.hide();
		});
		this.searchResult.on('click','li',function() {
			var index = parseInt($(this).attr('index'));
			if($(this).attr('check') == '') {
				$(this).attr('check', 'check');
				$(this).find('span').css({
					"background": "#0ed20e"
				})
			} else {
				$(this).attr('check', '');
				$(this).find('span').css({
					"background": "#fff"
				})
			}
			self.bindClick(index);
		})
		
		this.selectDown.click(function(event){
			// console.log(event.target)
		    event.stopPropagation();
		});
		$(document).keydown(function(event){ 
			self.firstCon.hide();
		});
		$(document).keyup(function(event){ 
			var searchKey = self.searchArea.attr('value');
			if(event.which != 116) {
				self.search(searchKey);
			}
			
		}); 
		this.titleBtn.click(function(e) {
			if($(e.target).is('input')) return;
			$(this).next('ul').toggle();
			var show = $(this).next('ul').css('display');
			if($(this).next().is('ul')){
				if(show == 'block') {
					$(this).find('i').html('-')
				} else {
					$(this).find('i').html('+')
				}
			}
		});
		this.box.click(function() {
			self.setChecked($(this));
		});
		this.topTitle.hover(function() {
			$(this).attr('title', self.topTitleText)
		});
		this.hasNext(this.titleBtn)
	};
    SelectForGame.prototype = {
		bindClick: function(index) {
			$(this.lastTitle.eq(index)).trigger('click');
			this.setChecked(this.lastTitle.eq(index))
		},
		getCheckText: function() {
			this.topTitleText = '';
			var self = this;
			this.lastTitle.each(function(index, item) {
				if($(item).attr('checked') == 'checked') {
					self.topTitleText += $(item).next().text() + ', '
				}
					// if(this.searchArea.attr('value') == '') this.searchArea.attr('placeholder','请选择')
			})
			this.topTitleText = this.topTitleText.substring(0, this.topTitleText.length - 2)
		},
		getlastTextArray: function() {
			var self = this;
			this.lastTitle.each(function(index, item) {
				self.allText.push($(item).next().text())
				
			})
		},
		search: function(key) {
			var reasultStr = '';
			var textLength = this.allText.length;
			for(var i = 0; i < textLength; i++) {
				var hasKey = this.allText[i].indexOf(key);
				
				// console.log(hasKey)
				if(hasKey >= 0 && key != '') {
					this.searchResult.show();
					var isCheck = '';
					var c = this.lastTitle.eq(i).attr('checked');
					if(c == 'checked') isCheck = 'check'
					reasultStr += '<li check= ' + '"' + isCheck + '"' + ' ' + 'index=' + '"' + i + '"' + '>' + this.allText[i] + '<span class="circle"></span></li>'
				}
			}
			this.searchResult.html(reasultStr);
			this.searchResult.find('li').each(function(index, item) {
				if($(item).attr('check') == 'check') {
					$(item).find('span').css({
						'background': '#0ed20e'
					})
				}
			})
		},
		hasNext: function(obj) {
			obj.each(function(index, item) {
				if(!$(item).next().is('ul')) {
					$(item).find('i').html('')
				}
			})
		},
		setChecked: function(obj) {
			var childCheck = obj.parents('.title').next('ul').find('input:checkbox');
			var parentCheck = $(obj.parents('ul')[0]).siblings('span').find('input:checkbox');
			var parentsCheck = obj.parents('ul').siblings('span').find('input:checkbox');
			if(obj.prop('checked')) {
				obj.attr('checked', 'checked');
				parentsCheck.attr('checked', 'checked').prop('checked', true);
				childCheck.attr('checked', 'checked').prop('checked', true)
			} else {
			    var n =	$('.second-con').find('input:checkbox');
			    var nLength = [];
				obj.removeAttr('checked');
				if(this.hasSiblings(obj)) {
					parentCheck.removeAttr('checked').prop('checked', false)
				}
				childCheck.removeAttr('checked').prop('checked', false);
				n.each(function(index, item) {
			    	if($(item).attr('checked') == 'checked') {
			    		nLength.push($(item))
			    	}
			    })
			    if(nLength.length == 0) {
			    	$('.first-checked').removeAttr('checked').prop('checked', false)
			    }
			}
		},
		hasSiblings: function(obj) {
			var hasCheck = [];
			var hasOtherCheck = $(obj.parents('li')[0]).siblings('li').find('input:checkbox');
			hasOtherCheck.each(function(index, item) {
				if($(item).attr('checked')) {
					hasCheck.push($(item))
				} 
			});
			if(hasCheck.length > 0) {
				return false
			} 
			return true
		}
	};
    SelectForGame.init = function(obj) {
		var _this = this;
		obj.each(function() {
			console.log('init')
			new _this($(this));
		})
	};
	window.SelectForGame = SelectForGame
}(jQuery));