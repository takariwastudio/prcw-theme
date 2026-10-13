const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const CopyWebpackPlugin = require('copy-webpack-plugin');

module.exports = {
  ...defaultConfig,
  plugins: [
    ...defaultConfig.plugins,
    new CopyWebpackPlugin({
      patterns: [
        {
          from: 'src/blocks/**/*.php',
          to: ({ absoluteFilename }) => {
            // Normaliza separadores para Windows (\ vs /) y convierte src/blocks/hero/render.php -> build/blocks/hero/render.php
            const normalized = absoluteFilename.replace(/\\/g, '/');
            const relative = normalized.split('src/blocks/')[1];
            return `blocks/${relative}`;
          },
        },
      ],
    }),
  ],
};
