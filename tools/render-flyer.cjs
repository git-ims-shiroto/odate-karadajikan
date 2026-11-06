const { chromium } = require("playwright");
const path = require("path");
const { pathToFileURL } = require("url");

(async () => {
  let browser;
  try {
    browser = await chromium.launch({ headless: true, channel: "chrome" });
  } catch {
    browser = await chromium.launch({ headless: true });
  }

  const page = await browser.newPage({
    viewport: { width: 1200, height: 1500 },
    deviceScaleFactor: 1.5,
  });
  const flyerUrl = pathToFileURL(path.resolve("flyer.html")).href;
  await page.goto(flyerUrl, { waitUntil: "load" });
  await page.emulateMedia({ media: "screen" });

  const flyer = page.locator(".flyer");
  await flyer.screenshot({
    path: path.resolve("flyer-preview.png"),
    animations: "disabled",
  });

  const metrics = await flyer.evaluate((element) => ({
    width: element.getBoundingClientRect().width,
    height: element.getBoundingClientRect().height,
    scrollHeight: element.scrollHeight,
  }));
  console.log(JSON.stringify(metrics));
  await browser.close();
})().catch((error) => {
  console.error(error);
  process.exitCode = 1;
});
