import globals from "globals";
import pluginVue from "eslint-plugin-vue";

export default [
  {
    files: ["**/*.{js,mjs,cjs,vue}"],
    languageOptions: {
      globals: globals.browser
    }
  },
  ...pluginVue.configs["flat/essential"],
  {
    rules: {
      "vue/multi-word-component-names": "off",
      "no-unused-vars": "warn",
      "no-undef": "warn"
    }
  }
];
