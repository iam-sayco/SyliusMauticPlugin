class MauticTracker {
    config: MauticConfig;
    pageviews: string[];
    available: boolean;

    constructor(config: MauticConfig) {
        this.config = config;
        this.config.defaultParameters = {
            page_url: window.location.href,
            page_title: document.title,
            referrer: document.referrer
        }

        this.pageviews = [];
        // @ts-ignore
        this.available = typeof window.mt === "function";

        if (this.available) {
            this.bindTrackingEvents();
            this.trackPageview();
        }
    }

    mtSend(extraParams: MauticParams = {}): void {
        const params: MauticParams = { ...this.config.defaultParameters, ...extraParams };
        // @ts-ignore
        mt("send", "pageview", params);
    }

    trackPageview(): void {
        this.mtSend();
    }

    bindTrackingEvents(): void {
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll("a").forEach(el => {
                el.addEventListener("mousedown", event => {
                    const url = (event.target as HTMLAnchorElement).href;
                    this.trackLink(url);
                });
            });
        });
    }

    trackLink(url: string): void {
        const resolver: UrlResolver = new UrlResolver(this.config);

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

    trackDownload(url: string): void {
        if (!this.config.trackDownload) return;
        this.mtSend({
            page_url: url,
            page_title: `Download ${url}`,
            referrer: window.location.href
        });
    }

    trackMailto(url: string): void {
        if (!this.config.trackMailto) return;
        this.mtSend({
            page_url: url,
            page_title: `Mail to: ${url.substr(7)}`,
            referrer: window.location.href
        });
    }

    trackTel(url: string): void {
        if (!this.config.trackTel) return;
        this.mtSend({
            page_url: url,
            page_title: `Phone: ${url.substr(5)}`,
            referrer: window.location.href
        });
    }

    trackExternalLink(url: string): void {
        if (!this.config.trackOutbound) return;
        this.mtSend({
            page_url: url,
            page_title: `External link: ${url}`,
            referrer: window.location.href
        });
    }
}

class UrlResolver {
    config: MauticConfig;

    constructor(config: MauticConfig) {
        this.config = config;
    }

    isInternal(url: string): boolean {
        try {
            const parsedUrl = new URL(url);
            return !parsedUrl.host || parsedUrl.host === window.location.host;
        } catch (error) {
            // Parsing failed, assume it's a relative path.
            return true;
        }
    }


    isDownload(url: string): boolean {
        const extensions: string[] = this.config.trackDownloadExtensions;
        const escapedExtensions: string = extensions.map(ext => ext.replace(/\./g, "\\.")).join("|");
        const regexPattern: string = `(${escapedExtensions})$`;
        const regex: RegExp = new RegExp(regexPattern, "i");
        return regex.test(url);
    }

    isMailto(url: string): boolean {
        const isInternalRegex: RegExp = /^mailto:/i;
        return isInternalRegex.test(url);
    }

    isTel(url: string): boolean {
        const isInternalRegex: RegExp = /^tel:/i;
        return isInternalRegex.test(url);
    }
}

interface MauticParams {
    page_url?: string;
    page_title?: string;
    referrer?: string;
}

interface MauticConfig {
    available: boolean;
    trackOutbound: boolean;
    trackDownload: boolean;
    trackMailto: boolean;
    trackTel: boolean;
    trackDownloadExtensions: string[];
    defaultParameters: MauticParams;
}
