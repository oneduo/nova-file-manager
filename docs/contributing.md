# Contributing

## Running the package on a local Laravel Nova instance

If you are looking to contribute to this package and run the tool on your local Laravel Nova installation, you may start
by forking the repository and configure your `composer.json` repositories section.

It may look like this :

```json
{
  "repositories": [
    {
      "type": "git",
      "url": "<your-fork-repository-git-url>"
    }
  ]
}
```

## Building locally

When building locally, the following requirements are to be met :

- NodeJS `18.3.0`
- PHP `^8.0`

You may use [nvm](https://github.com/nvm-sh/nvm) to automatically set your node version.

Next step is running the following command

```bash
nvm use
```

You are also required to meet the requirements of the `package-lock.json`.

You may run the following command to install the dependencies :

```bash
npm ci
npm run nova:install:dependency
```

And then to build the package :

```bash
npm run build
```

## Reference
If you are encountering any issues, please refer to the [Laravel Nova Documentation](https://nova.laravel.com/docs/4.0/customization/frontend.html#using-nova-mixins). Or you may open a new issue/discussion at https://github.com/BBS-Lab/nova-file-manager