module.exports = {
  apps : [
    {
      name: "TRANSACTIONS",
      script: "./transactions/service.js",
      instance_var: '0',
      watch: false,
      env: {
        NODE_CONFIG_DIR:"./transactions/config"
      }
    },
    {
      name: "DEPOT",
      script: "./depot/service.js",
      instance_var: '0',
      watch: false,
      env: {
        NODE_CONFIG_DIR:"./depot/config"
      }
    },
    {
      name: "READERS",
      script: "./readers/service.js",
      instance_var: '0',
      watch: false,
      env: {
        NODE_CONFIG_DIR:"./readers/config"
      }
    },
    {
      name: "LOCKERS",
      script: "./lockers/service.js",
      instance_var: '0',
      watch: false,
      env: {
        NODE_CONFIG_DIR:"./lockers/config"
      }
    },
    {
      name: "TERMINAL1",
      script: "./terminals1/service.js",
      instance_var: '0',
      watch: false,
      env: {
        NODE_CONFIG_DIR:"./terminals1/config"
      }
    },
    {
      name: "TERMINAL2",
      script: "./terminals2/service.js",
      instance_var: '0',
      watch: false,
      env: {
        NODE_CONFIG_DIR:"./terminals2/config"
      }
    },
  ]
};
