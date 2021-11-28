import _ from "lodash";

export default (name) => {
    const isPackage = name.includes('::');

    if(! isPackage) {
        return import(`./Pages/${name}.vue`).then(module => module.default)
    }

    const neewtonMetaTag = document.querySelector('meta[name="neewton-modules"]')

    if(! neewtonMetaTag) {
        throw new Error("Modules meta tag not found.")
    }

    const decrypted = atob(neewtonMetaTag.content)
    const [moduleName, modulePage] = name.split('::');
    const neewtonModule = _.find(JSON.parse(decrypted), { name: moduleName });

    if(! neewtonModule) {
        throw new Error(`Module ${moduleName} not found.`)
    }

    return import(`/../vendor/${neewtonModule.vendor}/src/${neewtonModule.resourcePath}/${modulePage}.vue`)
        .then(module => module.default)
}
