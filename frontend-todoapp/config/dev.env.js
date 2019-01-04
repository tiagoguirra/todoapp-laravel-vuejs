'use strict'
const merge = require('webpack-merge')
const prodEnv = require('./prod.env')

module.exports = merge(prodEnv, {
  NODE_ENV: '"development"',
  VUE_APP_ENV: '"production"',
  VUE_APP_ROOT_API: '"http://api.todoapp.test/api"'
})
