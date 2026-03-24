'use strict';
// HERO SLIDER
var hSlides,hTotal,hCurrent=0,hTimer;
function hSlide(dir){
  if(!hSlides||hTotal<2)return;
  hSlides[hCurrent].classList.remove('active');
  hCurrent=(hCurrent+dir+hTotal)%hTotal;
  hSlides[hCurrent].classList.add('active');
  var el=document.getElementById('sliderCounter');
  if(el)el.textContent=(hCurrent+1)+' / '+hTotal;
}
function initHeroSlider(){
  var wrap=document.getElementById('heroSlider');
  if(!wrap)return;
  hSlides=Array.from(wrap.querySelectorAll('.hero-slide'));
  hTotal=hSlides.length;
  if(hTotal<2)return;
  hTimer=setInterval(function(){hSlide(1)},5000);
  wrap.addEventListener('mouseenter',function(){clearInterval(hTimer)});
  wrap.addEventListener('mouseleave',function(){hTimer=setInterval(function(){hSlide(1)},5000)});
  // Touch
  var sx=0;
  wrap.addEventListener('touchstart',function(e){sx=e.touches[0].clientX},{passive:true});
  wrap.addEventListener('touchend',function(e){var d=sx-e.changedTouches[0].clientX;if(Math.abs(d)>40)hSlide(d>0?1:-1)},{passive:true});
}

// FLASH SALE TIMER
function initTimer(){
  var end=new Date();end.setDate(end.getDate()+3);end.setHours(23,59,59,0);
  function tick(){
    var now=Date.now(),diff=Math.max(0,end-now);
    var d=Math.floor(diff/86400000),h=Math.floor((diff%86400000)/3600000),m=Math.floor((diff%3600000)/60000),s=Math.floor((diff%60000)/1000);
    ['t-days','t-hours','t-mins','t-secs'].forEach(function(id,i){
      var el=document.getElementById(id);if(el)el.textContent=String([d,h,m,s][i]).padStart(2,'0');
    });
    ['d-days','d-hours','d-mins','d-secs'].forEach(function(id,i){
      var el=document.getElementById(id);if(el)el.textContent=String([d,h,m,s][i]).padStart(2,'0');
    });
  }
  tick();setInterval(tick,1000);
}

// DROPS NAV
var dropsPos=0;
function dropsNav(dir){
  var el=document.getElementById('dropsScroll');
  if(!el)return;
  var cardW=el.children[0]?el.children[0].offsetWidth+10:200;
  dropsPos=Math.max(0,Math.min(dropsPos+dir*cardW,(el.children.length-4)*cardW));
  el.style.transform='translateX(-'+dropsPos+'px)';
  el.style.transition='transform .3s ease';
}

document.addEventListener('DOMContentLoaded',function(){
  initHeroSlider();
  initTimer();
});
