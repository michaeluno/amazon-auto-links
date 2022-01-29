/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/control/PostControl.js":
/*!************************************!*\
  !*** ./src/control/PostControl.js ***!
  \************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */



const postToControlOption = post => ({
  label: post.title.raw,
  value: post.id
});

const ComboboxWrapper = _ref => {
  let {
    children
  } = _ref;
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "aal-gutenberg-combobox-wrapper"
  }, children);
};

const PostControl = _ref2 => {
  let {
    label,
    posts,
    value,
    onChange
  } = _ref2;
  const [options, setOptions] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)([]);
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (posts) {
      setOptions(posts.map(postToControlOption));
    }
  }, [posts]);
  if (typeof posts === null) return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Spinner, null);
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(ComboboxWrapper, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.ComboboxControl, {
    label: label,
    value: value,
    onChange: onChange,
    options: options
  }));
};

/* harmony default export */ __webpack_exports__["default"] = (PostControl);

/***/ }),

/***/ "./src/function/getMaxNumberOfItemsControl.js":
/*!****************************************************!*\
  !*** ./src/function/getMaxNumberOfItemsControl.js ***!
  \****************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "getMaxNumberOfItemsControl": function() { return /* binding */ getMaxNumberOfItemsControl; }
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);


/**
 * WordPress dependencies
 */


function getMaxNumberOfItemsControl(attributes, setAttributes) {
  const {
    count
  } = attributes;
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
    className: "aal-gutenberg-number-control",
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Maximum Number of Items', 'amazon-auto-links'),
    type: "number",
    value: count,
    min: "1",
    max: aalGutenbergUnitBlock.maxNumberOfItems,
    step: "1",
    onChange: value => {
      setAttributes({
        count: parseInt(value)
      });
    }
  });
}

/***/ }),

/***/ "./src/function/getPosts.js":
/*!**********************************!*\
  !*** ./src/function/getPosts.js ***!
  \**********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "getPosts": function() { return /* binding */ getPosts; }
/* harmony export */ });
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/data */ "@wordpress/data");
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__);
/**
 * WordPress dependencies
 */

function getPosts(postType) {
  const posts = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.useSelect)(select => // for arguments, @see https://developer.wordpress.org/rest-api/reference/posts/#arguments
  select("core").getEntityRecords("postType", postType, {
    per_page: -1,
    orderby: "date",
    // e.g. "title",
    order: "desc",
    //	e.g. "asc",
    status: "publish"
  }), [postType]);
  return posts;
}

/***/ }),

/***/ "./src/icons.js":
/*!**********************!*\
  !*** ./src/icons.js ***!
  \**********************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "iconAmazon": function() { return /* binding */ iconAmazon; }
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);

/**
 * @remark Somehow, without the createElement() function, an error occurs in the browser console, saying "SVG is not defined."
 * @see gutenberg-trunk/packages/block-library/src/embed/variations.js
 * @see https://github.com/WordPress/gutenberg/blob/v12.4.1/packages/block-library/src/embed/variations.js
 *
 * ```
 * export const embedAmazonIcon = (
 * <SVG viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
 *   <Path d="M18.42 14.58c-.51-.66-1.05-1.23-1.05-2.5V7.87c0-1.8.15-3.45-1.2-4.68-1.05-1.02-2.79-1.35-4.14-1.35-2.6 0-5.52.96-6.12 4.14-.06.36.18.54.4.57l2.66.3c.24-.03.42-.27.48-.5.24-1.12 1.17-1.63 2.2-1.63.56 0 1.22.21 1.55.7.4.56.33 1.31.33 1.97v.36c-1.59.18-3.66.27-5.16.93a4.63 4.63 0 0 0-2.93 4.44c0 2.82 1.8 4.23 4.1 4.23 1.95 0 3.03-.45 4.53-1.98.51.72.66 1.08 1.59 1.83.18.09.45.09.63-.1v.04l2.1-1.8c.24-.21.2-.48.03-.75zm-5.4-1.2c-.45.75-1.14 1.23-1.92 1.23-1.05 0-1.65-.81-1.65-1.98 0-2.31 2.1-2.73 4.08-2.73v.6c0 1.05.03 1.92-.5 2.88z" />
 *   <Path d="M21.69 19.2a17.62 17.62 0 0 1-21.6-1.57c-.23-.2 0-.5.28-.33a23.88 23.88 0 0 0 20.93 1.3c.45-.19.84.3.39.6z" />
 *	 <Path d="M22.8 17.96c-.36-.45-2.22-.2-3.1-.12-.23.03-.3-.18-.05-.36 1.5-1.05 3.96-.75 4.26-.39.3.36-.1 2.82-1.5 4.02-.21.18-.42.1-.3-.15.3-.8 1.02-2.58.69-3z" />
 * </SVG>
 * );
 * ```
 */

const iconAmazon = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)('svg', {
  width: 24,
  height: 24
}, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)('path', {
  d: "M18.42 14.58c-.51-.66-1.05-1.23-1.05-2.5V7.87c0-1.8.15-3.45-1.2-4.68-1.05-1.02-2.79-1.35-4.14-1.35-2.6 0-5.52.96-6.12 4.14-.06.36.18.54.4.57l2.66.3c.24-.03.42-.27.48-.5.24-1.12 1.17-1.63 2.2-1.63.56 0 1.22.21 1.55.7.4.56.33 1.31.33 1.97v.36c-1.59.18-3.66.27-5.16.93a4.63 4.63 0 0 0-2.93 4.44c0 2.82 1.8 4.23 4.1 4.23 1.95 0 3.03-.45 4.53-1.98.51.72.66 1.08 1.59 1.83.18.09.45.09.63-.1v.04l2.1-1.8c.24-.21.2-.48.03-.75zm-5.4-1.2c-.45.75-1.14 1.23-1.92 1.23-1.05 0-1.65-.81-1.65-1.98 0-2.31 2.1-2.73 4.08-2.73v.6c0 1.05.03 1.92-.5 2.88z"
}), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)('path', {
  d: "M21.69 19.2a17.62 17.62 0 0 1-21.6-1.57c-.23-.2 0-.5.28-.33a23.88 23.88 0 0 0 20.93 1.3c.45-.19.84.3.39.6z"
}), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)('path', {
  d: "M22.8 17.96c-.36-.45-2.22-.2-3.1-.12-.23.03-.3-.18-.05-.36 1.5-1.05 3.96-.75 4.26-.39.3.36-.1 2.82-1.5 4.02-.21.18-.42.1-.3-.15.3-.8 1.02-2.58.69-3z"
}));

/***/ }),

/***/ "./src/index.js":
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/block-editor */ "@wordpress/block-editor");
/* harmony import */ var _wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/server-side-render */ "@wordpress/server-side-render");
/* harmony import */ var _wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _editor_scss__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./editor.scss */ "./src/editor.scss");
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./style.scss */ "./src/style.scss");
/* harmony import */ var _icons__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./icons */ "./src/icons.js");
/* harmony import */ var _block_json__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../block.json */ "./block.json");
/* harmony import */ var _control_PostControl__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./control/PostControl */ "./src/control/PostControl.js");
/* harmony import */ var _function_getPosts__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./function/getPosts */ "./src/function/getPosts.js");
/* harmony import */ var _function_getMaxNumberOfItemsControl__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./function/getMaxNumberOfItemsControl */ "./src/function/getMaxNumberOfItemsControl.js");


/**
 * WordPres dependencies
 */





/**
 * Internal dependencies
 */





const {
  name
} = _block_json__WEBPACK_IMPORTED_MODULE_9__;




const getUnitSelectControl = (attributes, setAttributes) => {
  const {
    id
  } = attributes;
  const units = (0,_function_getPosts__WEBPACK_IMPORTED_MODULE_11__.getPosts)('amazon_auto_links');
  return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_control_PostControl__WEBPACK_IMPORTED_MODULE_10__["default"], {
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Unit Name', 'amazon-auto-links'),
    value: id,
    posts: units,
    onChange: value => {
      setAttributes({
        id: parseInt(value) || 0
      });
    }
  });
};

const emptyResponsePlaceholder = () => (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.Placeholder, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Select a unit.', 'amazon-auto-links')));

(0,_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__.registerBlockType)(name, {
  icon: _icons__WEBPACK_IMPORTED_MODULE_8__.iconAmazon,
  attributes: {
    id: {
      type: 'integer',
      default: 0
    },
    count: {
      type: 'integer'
    }
  },

  edit(_ref) {
    let {
      name,
      attributes,
      setAttributes
    } = _ref;
    const blockProps = (0,_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__.useBlockProps)();
    return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", blockProps, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_block_editor__WEBPACK_IMPORTED_MODULE_4__.InspectorControls, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_3__.PanelBody, {
      title: 'Combo Box Setting'
    }, getUnitSelectControl(attributes, setAttributes), (0,_function_getMaxNumberOfItemsControl__WEBPACK_IMPORTED_MODULE_12__.getMaxNumberOfItemsControl)(attributes, setAttributes))), getUnitSelectControl(attributes, setAttributes), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)((_wordpress_server_side_render__WEBPACK_IMPORTED_MODULE_5___default()), {
      block: name,
      attributes: attributes,
      EmptyResponsePlaceholder: emptyResponsePlaceholder
    }));
  }

});

/***/ }),

/***/ "./src/editor.scss":
/*!*************************!*\
  !*** ./src/editor.scss ***!
  \*************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./src/style.scss":
/*!************************!*\
  !*** ./src/style.scss ***!
  \************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "@wordpress/block-editor":
/*!*************************************!*\
  !*** external ["wp","blockEditor"] ***!
  \*************************************/
/***/ (function(module) {

module.exports = window["wp"]["blockEditor"];

/***/ }),

/***/ "@wordpress/blocks":
/*!********************************!*\
  !*** external ["wp","blocks"] ***!
  \********************************/
/***/ (function(module) {

module.exports = window["wp"]["blocks"];

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ (function(module) {

module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/data":
/*!******************************!*\
  !*** external ["wp","data"] ***!
  \******************************/
/***/ (function(module) {

module.exports = window["wp"]["data"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ (function(module) {

module.exports = window["wp"]["element"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ (function(module) {

module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "@wordpress/server-side-render":
/*!******************************************!*\
  !*** external ["wp","serverSideRender"] ***!
  \******************************************/
/***/ (function(module) {

module.exports = window["wp"]["serverSideRender"];

/***/ }),

/***/ "./block.json":
/*!********************!*\
  !*** ./block.json ***!
  \********************/
/***/ (function(module) {

module.exports = JSON.parse('{"$schema":"https://json.schemastore.org/block.json","apiVersion":2,"title":"Auto Amazon Links: Unit","name":"auto-amazon-links/unit","editorScript":"file:./build/index.js","editorStyle":"file:./build/index.css","style":"file:./build/style-index.css"}');

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	!function() {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = function(result, chunkIds, fn, priority) {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var chunkIds = deferred[i][0];
/******/ 				var fn = deferred[i][1];
/******/ 				var priority = deferred[i][2];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every(function(key) { return __webpack_require__.O[key](chunkIds[j]); })) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	!function() {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"index": 0,
/******/ 			"style-index": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = function(chunkId) { return installedChunks[chunkId] === 0; };
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = function(parentChunkLoadingFunction, data) {
/******/ 			var chunkIds = data[0];
/******/ 			var moreModules = data[1];
/******/ 			var runtime = data[2];
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some(function(id) { return installedChunks[id] !== 0; })) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunk"] = self["webpackChunk"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	}();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["style-index"], function() { return __webpack_require__("./src/index.js"); })
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;
//# sourceMappingURL=index.js.map