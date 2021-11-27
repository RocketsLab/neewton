import _ from "lodash";

export default (name) => {
    const isPackage = name.includes('::');

    if(! isPackage) {
        return import(`./Pages/${name}.vue`).then(module => module.default)
    }

    const [moduleName, modulePage] = name.split('::');
    const neewtonModule = _.find(window.neewtonModules, { name: moduleName });

    if(! neewtonModule) {
        throw new Error(`Module ${moduleName} not found.`)
    }

    return import(`/../vendor/${neewtonModule.vendor}/src/${neewtonModule.resourcePath}/${modulePage}.vue`)
        .then(module => module.default)
}
