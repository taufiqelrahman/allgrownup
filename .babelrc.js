module.exports = {
  presets: [
    '@zeit/next-typescript/babel',
    [
      "next/babel",
      {
        "styled-jsx": {
          "plugins": [
            "styled-jsx-plugin-postcss"
          ]
        }
      }
    ]
  ],
}