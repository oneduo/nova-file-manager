let sidebar = [
  {
    text: "Getting Started",
    collapsible: true,
    items: [
      { text: "Introduction", link: "/introduction" },
      { text: "Requirements", link: "/requirements" },
      { text: "Installation", link: "/installation" },
      { text: "Authors & Credits", link: "/credits" },
    ],
  },
  {
    text: "Usage",
    collapsible: true,
    items: [
      { text: "Tool", link: "/tool" },
      { text: "Field", link: "/field" },
    ],
  },
  {
    text: "Misc",
    collapsible: true,
    items: [
      { text: "Configuration file", link: "/configuration" },
      { text: "Screenshots", link: "/screenshots" },
    ],
  },
  {
    text: "Links",
    collapsible: true,
    items: [
      { text: "Github", link: "https://github.com/BBS-Lab/nova-file-manager" },
      { text: "Other packages", link: "https://github.com/BBS-Lab" },
    ],
  },
];

let head = [
  ["meta", { property: "og:title", content: "Varnish" }],
  [
    "meta",
    {
      property: "og:description",
      content: "A library of UI components built using Vue.js and TailwindCSS.",
    },
  ],
  ["meta", { property: "og:url", content: "https://varnish.caneara.com" }],
  [
    "meta",
    { property: "og:image", content: "https://varnish.caneara.com/card.png" },
  ],
  [
    "meta",
    {
      property: "og:secure_url",
      content: "https://varnish.caneara.com/card.png",
    },
  ],
  ["meta", { name: "twitter:card", content: "summary_large_image" }],
  ["meta", { name: "twitter:title", content: "Varnish" }],
  [
    "meta",
    {
      name: "twitter:description",
      content: "A library of UI components built using Vue.js and TailwindCSS.",
    },
  ],
  [
    "meta",
    { name: "twitter:image", content: "https://varnish.caneara.com/card.png" },
  ],
  ["meta", { name: "twitter:creator", content: "@CanearaHQ" }],
  ["meta", { name: "twitter:site", content: "@CanearaHQ" }],
];

let theme = {
  sidebar: sidebar,
  socialLinks: [
    { icon: "github", link: "https://github.com/BBS-Lab/nova-file-manager" },
  ],
  editLink: {
    text: "Edit page",
    pattern:
      "https://github.com/BBS-Lab/nova-file-manager/edit/main/docs/:path",
  },
};

export default {
  head: head,
  title: "Nova File Manager",
  description:
    "A file manager tool and field for Laravel Nova. Beautifully designed, and customizable, this tool will provide a plug'n'play solution for your file management needs.",
  lastUpdated: false,
  themeConfig: theme,
  ignoreDeadLinks: true,
};
