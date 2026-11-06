const { chromium } = require('C:/Users/user/.cache/codex-runtimes/codex-primary-runtime/dependencies/node/node_modules/playwright');
const {spawn}=require('child_process');
const fs=require('fs');
const servers=[spawn('C:/xampp/php/php.exe',['-S','127.0.0.1:8097','-t','.local-wordpress/wordpress'],{windowsHide:true,stdio:'ignore'}),spawn('C:/xampp/php/php.exe',['-S','127.0.0.1:8098','-t','.'],{windowsHide:true,stdio:'ignore'})];
let browser;
(async()=>{
 await new Promise(r=>setTimeout(r,1000));
 browser=await chromium.launch({channel:'chrome',headless:true});
 const page=await browser.newPage();
 const mock=await browser.newPage();
 // Compare actual CSS with the reference and a real media-library attachment.
 for(const width of [320,390,768,1440,1920]){
  await page.setViewportSize({width,height:1000});await mock.setViewportSize({width,height:1000});
  await page.goto('http://127.0.0.1:8097/',{waitUntil:'networkidle'});
  await mock.goto('http://127.0.0.1:8098/index.html',{waitUntil:'domcontentloaded'});
  await Promise.all([page.evaluate(()=>document.fonts.ready),mock.evaluate(()=>document.fonts.ready)]);
  const measure=()=>{
   const css=s=>{const c=getComputedStyle(document.querySelector(s));return {font:c.fontFamily,weight:c.fontWeight,size:c.fontSize,color:c.color}};
   const el=document.querySelector('.hero-visual'),im=el.querySelector('img'),r=el.getBoundingClientRect(),i=im.getBoundingClientRect();
   return {body:css('body'),h1:css('h1'),h2:css('.section-heading h2'),accent:css('.hero h1 em'),cta:css('.button-line'),overflow:document.documentElement.scrollWidth>innerWidth,imageFill:Math.abs(r.width-i.width)<1&&Math.abs(r.height-i.height)<1,imageRadius:getComputedStyle(im.parentElement).borderRadius,imageFirst:r.y<document.querySelector('.hero-copy').getBoundingClientRect().y,fontReady:document.fonts.check('400 18px "Zen Maru Gothic"','体操教室'),naturalWidth:im.naturalWidth};
  };
  const actual=await page.evaluate(measure),expected=await mock.evaluate(measure);
  for(const key of ['body','h1','h2','accent','cta'])if(JSON.stringify(actual[key])!==JSON.stringify(expected[key]))throw Error(width+' '+key+' mismatch '+JSON.stringify({actual:actual[key],expected:expected[key]}));
  if(actual.overflow||!actual.imageFill||!actual.fontReady||actual.imageRadius!=='0px'||actual.imageFirst!==(width<=980))throw Error(JSON.stringify({width,...actual}));
  console.log(JSON.stringify({width,fontsMatch:true,fontLoaded:actual.fontReady,imageFillsColumn:actual.imageFill,mobileImageFirst:actual.imageFirst,overflow:actual.overflow}));
  if(width===390)await page.screenshot({path:'wordpress/mobile-preview.png',fullPage:false});
  if(width===1440){await page.screenshot({path:'.local-wordpress/desktop-design.png',fullPage:true});await page.locator('.hero').screenshot({path:'wordpress/hero-preview.png'});await mock.locator('.hero').screenshot({path:'.local-wordpress/mock-hero.png'});}
 }
 // Original copy under the same layout isolates design differences from editable wording.
 await page.setViewportSize({width:1440,height:1000});await page.goto('http://127.0.0.1:8097/',{waitUntil:'networkidle'});
 await page.evaluate(()=>{document.querySelector('h1').innerHTML='<span class="hero-title-line">無理なく、楽しく。</span><em>今日の身体にあう運動を。</em>';});
 await page.locator('.hero').screenshot({path:'.local-wordpress/same-copy-hero.png'});
})().then(async()=>{await browser.close();servers.forEach(s=>s.kill())}).catch(async e=>{console.error(e);if(browser)await browser.close();servers.forEach(s=>s.kill());process.exitCode=1});
