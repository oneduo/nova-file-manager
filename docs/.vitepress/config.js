let sidebar = [
  {
    text: 'Getting Started',
    collapsible: true,
    items: [
      {
        text: 'Introduction',
        link: '/introduction',
      },
      {
        text: 'Requirements',
        link: '/requirements',
      },
      {
        text: 'Installation',
        link: '/installation',
      },
      {
        text: 'Authors & Credits',
        link: '/credits',
      },
    ],
  },
  {
    text: 'Usage',
    collapsible: true,
    items: [
      {
        text: 'Tool',
        link: '/tool',
      },
      {
        text: 'Field',
        link: '/field',
      },
    ],
  },
  {
    text: 'Misc',
    collapsible: true,
    items: [
      {
        text: 'Configuration file',
        link: '/configuration',
      },
      {
        text: 'Localization',
        link: '/localization',
      },
      {
        text: 'Screenshots',
        link: '/screenshots',
      },
    ],
  },
  {
    text: 'Links',
    collapsible: true,
    items: [
      {
        text: 'Github',
        link: 'https://github.com/BBS-Lab/nova-file-manager',
      },
      {
        text: 'Other packages',
        link: 'https://github.com/BBS-Lab',
      },
    ],
  },
]

let head = [
  [
    'meta',
    {
      property: 'og:title',
      content: 'Nova File Manager',
    },
  ],
  [
    'meta',
    {
      property: 'og:description',
      content:
        'A handy Laravel Nova tool for all your file management needs, with multi-disk and chunk uploads supports',
    },
  ],
  [
    'meta',
    {
      property: 'og:url',
      content: 'https://github.com/BBS-Lab/nova-file-manager',
    },
  ],
  [
    'meta',
    {
      property: 'og:image',
      content: 'https://raw.githubusercontent.com/BBS-Lab/nova-file-manager/main/docs/cover.png',
    },
  ],
  [
    'meta',
    {
      name: 'twitter:card',
      content: 'summary_large_image',
    },
  ],
  [
    'script',
    {
      async: true,
      href: 'https://app.tinyanalytics.io/pixel/APc0MxOmPizf2RZ',
    },
  ],
]

let theme = {
  sidebar: sidebar,
  socialLinks: [
    {
      icon: 'github',
      link: 'https://github.com/BBS-Lab/nova-file-manager',
    },
  ],
  editLink: {
    text: 'Edit page',
    pattern: 'https://github.com/BBS-Lab/nova-file-manager/edit/main/docs/:path',
  },
  algolia: {
    apiKey: 'f9cb045fd7c64079aecb15f9e450cf29',
    indexName: 'nova-file-manager',
    appId: '3KZHHGDGMO',
  },
}

export default {
  head: head,
  title: 'Nova File Manager',
  description:
    "A file manager tool and field for Laravel Nova. Beautifully designed, and customizable, this tool will provide a plug'n'play solution for your file management needs.",
  lastUpdated: false,
  themeConfig: theme,
  ignoreDeadLinks: true,
  base: '/nova-file-manager/',
}
