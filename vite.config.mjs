import { createViteConfig } from "vite-config-factory";

const entries = {
    'css/modularity-step-by-step': './source/sass/modularity-step-by-step.scss',
    'js/modularity-step-by-step-admin': './source/js/admin.ts',
    'js/modularity-step-by-step': './source/js/step-by-step.ts',
};

export default createViteConfig(entries, {
    outDir: "assets/dist",
    manifestFile: "manifest.json",
});
