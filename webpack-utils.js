// Utilities.
const path = require( 'path' );

const { fromProjectRoot } = require("@wordpress/scripts/utils/file");

function getProjectSourcePath() {
  return process.env.WP_SOURCE_PATH || 'src';
}

const { sync: glob } = require("fast-glob");

function getStylesEntryPoints() {
  // Checks whether any block metadata files can be detected in the defined source directory.
  const stylePaths = glob("css/**/!(_*).scss", {
    absolute: false,
    cwd: fromProjectRoot(getProjectSourcePath()),
  });

  let entryPoints = {};

  if (stylePaths.length > 0) {
    for (const stylePath of stylePaths) {
      let fileName = path.basename(stylePath, path.extname(stylePath));

      entryPoints = {
        ...entryPoints,
        ...{
          [path.join(path.dirname(stylePath), fileName)]: path.resolve(
            fromProjectRoot(getProjectSourcePath()),
            stylePath,
          ),
        },
      };
    }
  }

  return entryPoints;
}

function getScriptsEntryPoints() {
  // Checks whether any block metadata files can be detected in the defined source directory.
  const scriptsPaths = glob("js/**/*.js", {
    absolute: false,
    cwd: fromProjectRoot(getProjectSourcePath()),
  });

  let entryPoints = {};

  if (scriptsPaths.length > 0) {
    for (const scriptsPath of scriptsPaths) {
      let fileName = path.basename(scriptsPath, path.extname(scriptsPath));

      entryPoints = {
        ...entryPoints,
        ...{
          [path.join(path.dirname(scriptsPath), fileName)]: path.resolve(
            fromProjectRoot(getProjectSourcePath()),
            scriptsPath,
          ),
        },
      };
    }
  }

  return entryPoints;
}

module.exports = {
  getStylesEntryPoints,
  getScriptsEntryPoints
};