module.exports = {
    apps : [
      {
        name: "TRANSACTIONS",
        script: "./transactions/service.js",
        instance_var: '0',
        watch: false,
        env: {
          NODE_CONFIG_DIR:"./transactions/config",
          NODE_ENV: "production"
        }
      },
      {
        name: "DEPOT",
        script: "./depot/service.js",
        instance_var: '0',
        watch: false,
        env: {
          NODE_CONFIG_DIR:"./depot/config",
          NODE_ENV: "production"
        }
      },
      {
        name: "READERS",
        script: "./readers/service.js",
        instance_var: '0',
        watch: false,
        env: {
          NODE_CONFIG_DIR:"./readers/config",
          NODE_ENV: "production"
        }
      },
      {
        name: "LOCKERS",
        script: "./lockers/service.js",
        instance_var: '0',
        watch: false,
        env: {
          NODE_CONFIG_DIR:"./lockers/config",
          NODE_ENV: "production"
        }
      },
      {
        name: "TERMINAL1",
        script: "./terminals1/service.js",
        instance_var: '0',
        watch: false,
        env: {
          NODE_CONFIG_DIR:"./terminals1/config",
          NODE_ENV: "production"
        }
      },
      {
        name: "TERMINAL2",
        script: "./terminals2/service.js",
        instance_var: '0',
        watch: false,
        env: {
          NODE_CONFIG_DIR:"./terminals2/config",
          NODE_ENV: "production"
        }
      },
    ]
  };
  