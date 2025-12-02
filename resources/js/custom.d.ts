// resources/js/custom.d.ts

// Esta declaración le dice a TypeScript que cualquier importación 
// de un archivo .module.css debe ser tratada como un objeto 
// donde cada clave (el nombre de tu clase CSS) es de tipo string.
declare module '*.module.css' {
    const classes: { readonly [key: string]: string };
    export default classes;
}

// Opcionalmente, puedes hacer lo mismo para otros preprocesadores 
// que uses con módulos (ejemplo con SCSS):
declare module '*.module.scss' {
    const classes: { readonly [key: string]: string };
    export default classes;
}