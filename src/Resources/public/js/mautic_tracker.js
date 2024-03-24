class MauticTracker {
    constructor(config) {
        this.config = config;
        this.config.defaultParameters = {
            page_url: window.location.href,
            page_title: document.title,
            referrer: document.referrer
        };
        this.pageviews = [];
        // @ts-ignore
        this.available = typeof window.mt === "function";
        if (this.available) {
            this.bindTrackingEvents();
            this.trackPageview();
        }
    }
    mtSend(extraParams = {}) {
        const params = Object.assign(Object.assign({}, this.config.defaultParameters), extraParams);
        // @ts-ignore
        mt("send", "pageview", params);
    }
    trackPageview() {
        this.mtSend();
    }
    bindTrackingEvents() {
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll("a").forEach(el => {
                el.addEventListener("mousedown", event => {
                    const url = event.target.href;
                    this.trackLink(url);
                });
            });
        });
    }
    trackLink(url) {
        const resolver = new UrlResolver(this.config);
        if (false === resolver.isInternal(url)) {
            this.trackExternalLink(url);
            return;
        }
        if (resolver.isDownload(url)) {
            this.trackDownload(url);
            return;
        }
        if (resolver.isMailto(url)) {
            this.trackMailto(url);
            return;
        }
        if (resolver.isTel(url)) {
            this.trackTel(url);
            return;
        }
        return;
    }
    trackDownload(url) {
        if (!this.config.trackDownload)
            return;
        this.mtSend({
            page_url: url,
            page_title: `Download ${url}`,
            referrer: window.location.href
        });
    }
    trackMailto(url) {
        if (!this.config.trackMailto)
            return;
        this.mtSend({
            page_url: url,
            page_title: `Mail to: ${url.substr(7)}`,
            referrer: window.location.href
        });
    }
    trackTel(url) {
        if (!this.config.trackTel)
            return;
        this.mtSend({
            page_url: url,
            page_title: `Phone: ${url.substr(5)}`,
            referrer: window.location.href
        });
    }
    trackExternalLink(url) {
        if (!this.config.trackOutbound)
            return;
        this.mtSend({
            page_url: url,
            page_title: `External link: ${url}`,
            referrer: window.location.href
        });
    }
}
class UrlResolver {
    constructor(config) {
        this.config = config;
    }
    isInternal(url) {
        try {
            const parsedUrl = new URL(url);
            return !parsedUrl.host || parsedUrl.host === window.location.host;
        }
        catch (error) {
            // Parsing failed, assume it's a relative path.
            return true;
        }
    }
    isDownload(url) {
        const extensions = this.config.trackDownloadExtensions;
        const escapedExtensions = extensions.map(ext => ext.replace(/\./g, "\\.")).join("|");
        const regexPattern = `(${escapedExtensions})$`;
        const regex = new RegExp(regexPattern, "i");
        return regex.test(url);
    }
    isMailto(url) {
        const isInternalRegex = /^mailto:/i;
        return isInternalRegex.test(url);
    }
    isTel(url) {
        const isInternalRegex = /^tel:/i;
        return isInternalRegex.test(url);
    }
}
