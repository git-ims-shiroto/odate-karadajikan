if (process.argv.includes('--design')) { require('./verify-design.cjs'); } else {
const fs = require('fs');
const { chromium } = require('C:/Users/user/.cache/codex-runtimes/codex-primary-runtime/dependencies/node/node_modules/playwright');
const server = require('child_process').spawn('C:/xampp/php/php.exe',['-S','127.0.0.1:8097','-t','.local-wordpress/wordpress'],{windowsHide:true,stdio:'ignore'});
(async () => {
 await new Promise(r=>setTimeout(r,1200));
 const browser = await chromium.launch({channel:'chrome', headless:true});
 const page = await browser.newPage();
 const errors=[]; page.on('pageerror',e=>errors.push(e.message));
 const base='http://127.0.0.1:8097';
 for (const width of [320,390,768,1440]) {
  await page.setViewportSize({width,height:900});
  await page.goto(base,{waitUntil:'networkidle'});
  const check=await page.evaluate(()=>({sections:document.querySelectorAll('main > section').length,h1:document.querySelector('h1')?.textContent,overflow:document.documentElement.scrollWidth>innerWidth,brokenImages:[...document.images].filter(i=>!i.complete||i.naturalWidth===0).length,ctaHeight:document.querySelector('.mobile-cta').getBoundingClientRect().height}));
  if(check.sections!==8||check.overflow||check.brokenImages||!check.h1.includes('大館市'))throw Error(JSON.stringify({width,...check}));
  console.log(JSON.stringify({width,...check}));
  if(width===390)await page.screenshot({path:'.local-wordpress/mobile.png',fullPage:true});
  if(width===1440)await page.screenshot({path:'.local-wordpress/desktop.png',fullPage:true});
 }
 await page.locator('.faq-item summary').first().click();
 if(!await page.locator('.faq-item').first().evaluate(e=>e.open))throw Error('FAQ failed');
 await page.locator('.hero .button-line').click();
 if(!page.url().endsWith('#line-status'))throw Error('Pending LINE target failed');
 const credentials=JSON.parse(fs.readFileSync('.local-wordpress/credentials.json','utf8'));
 await page.goto(base+'/wp-login.php');
 await page.locator('#user_login').fill(credentials.user); await page.locator('#user_pass').fill(credentials.password);
 await Promise.all([page.waitForURL('**/wp-admin/'),page.locator('#wp-submit').click()]);
 await page.goto(base+'/wp-admin/themes.php?page=ok-lp');
 if(!await page.locator('h1').filter({hasText:'教室LP設定'}).count())throw Error('Settings page missing');
 console.log('Settings controls: '+await page.locator('textarea,input[name^="ok_lp_content"]').count());
 await page.locator('.ok-section').first().locator('summary').first().click();
 await page.locator('textarea[name="ok_lp_content[general][site_name]"]').fill('大館テスト教室');
 await page.locator('input[name="ok_lp_content[general][line_url]"]').fill('https://lin.ee/theme-test');
 await Promise.all([page.waitForURL('**/themes.php?page=ok-lp&settings-updated=true'),page.locator('#submit').click()]);
 await page.goto(base);
 if(!await page.locator('.brand').first().textContent().then(s=>s.includes('大館テスト教室')))throw Error('Save failed');
 if(await page.locator('.hero .button-line').getAttribute('href')!=='https://lin.ee/theme-test')throw Error('LINE save failed');
 console.log('Actual WordPress login, settings save, front-end update: PASS');
 await page.goto(base+'/wp-admin/themes.php?page=ok-lp');
 await page.locator('.ok-section').first().locator('summary').first().click();
 await page.locator('textarea[name="ok_lp_content[general][site_name]"]').fill('大館 からだの調律時間');
 await page.locator('input[name="ok_lp_content[general][line_url]"]').fill('');
 await Promise.all([page.waitForURL('**/themes.php?page=ok-lp&settings-updated=true'),page.locator('#submit').click()]);
 if(errors.length)throw Error(errors.join('\n'));
 await browser.close(); server.kill();
})().catch(e=>{console.error(e);server.kill();process.exit(1)});

}
