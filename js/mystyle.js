'use strict';

const target = document.querySelectorAll('#navi nav ul li');
target.forEach(function (item, index) {
        item.onmouseover = function () {
                let ufo = this;
                ufo.classList.add('a1');
                setTimeout(function () {
                        ufo.classList.remove('a1');
                        ufo.classList.add('a2');
                }, 200);
                setTimeout(function () {
                        ufo.classList.remove('a2');
                        ufo.classList.add('a3');
                }, 300);
        }
});
target.forEach(function (item, index) {
        item.onmouseleave = function () {
                let ufo = this;
                ufo.classList.remove('a3');
        }
});

//(jquery版)
/*	$(document).ready(function(){
		$('#navi nav ul li').on('mouseover',function(){
			$(this).css('background',`url(nav_ufo.gif?${(new Date).getTime()})`);

		});
        
		$('#navi nav ul li').on('mouseout',function(){
			$(this).css('background','none');
		});
	});*/

/*===== ソート ======*/

const disc = document.querySelector('select').name;

if(disc === 'all') {
    document.querySelector('option[value="discography.html"]').selected = true;
} else if(disc === 'single') {
    document.querySelector('option[value="single.html"]').selected = true;
}else if(disc === 'album') {
    document.querySelector('option[value="album.html"]').selected = true;
}
document.getElementById('disc_sp_list').onchange = function(){
    location.href = document.getElementById('disc_sp_list').value;
}


