module.exports = {
    apps : [
      {
        name: "READERS",
        script: "./readers/service.js",
        instance_var: '0',
        watch: false,
        node_args: "--max-old-space-size=1024",
        env: {
          NODE_CONFIG_DIR:"./readers/config",
          NODE_ENV: "production",
          UV_THREADPOOL_SIZE: 128
        }
      },
      {
        name: "LOCKERS",
        script: "./lockers/service.js",
        instance_var: '0',
        watch: false,
        node_args: "--max-old-space-size=1024",
        env: {
          NODE_CONFIG_DIR:"./lockers/config",
          NODE_ENV: "production",
          UV_THREADPOOL_SIZE: 128
        }
      },
      {
        name: "TERMINAL1",
        script: "./terminals1/service.js",
        instance_var: '0',
        watch: false,
        env: {
          NODE_CONFIG_DIR:"./terminals1/config",
          NODE_ENV: "production",
          UV_THREADPOOL_SIZE: 64
        }
      },
      {
        name: "TERMINAL2",
        script: "./terminals2/service.js",
        instance_var: '0',
        watch: false,
        env: {
          NODE_CONFIG_DIR:"./terminals2/config",
          NODE_ENV: "production",
          UV_THREADPOOL_SIZE: 64
        }
      },
    ]
  };
  