# config valid only for current version of Capistrano
lock '3.4.0'

set :application, 'colors'
set :repo_url, 'git@github.com:caseygitflynn/colors-site.git'
set :branch, :master

SSHKit.config.command_map[:bash] = "/usr/bin/bash"

# Default branch is :master
# ask :branch, `git rev-parse --abbrev-ref HEAD`.chomp

# Default deploy_to directory is /var/www/my_app_name
# set :deploy_to, '/var/www/my_app_name'

# Default value for :scm is :git
# set :scm, :git

# Default value for :format is :pretty
# set :format, :pretty

# Default value for :log_level is :debug
# set :log_level, :debug

# Default value for :pty is false
# set :pty, true

# Default value for :linked_files is []
# set :linked_files, %w{}

# Default value for linked_dirs is []
set :linked_dirs, fetch(:linked_dirs, []).push('cache')

# Default value for default_env is {}
# set :default_env, { path: "/opt/ruby/bin:$PATH" }

# Default value for keep_releases is 5
# set :keep_releases, 5

set :colors_deployment_id, DateTime.now.to_time.to_i.to_s

namespace :composer do
  task :setup do
    on roles(:app) do
      execute "cd #{deploy_to} && curl -sS https://getcomposer.org/installer | php"
    end
  end

  task :deps do
    on roles(:app) do
      execute "php #{deploy_to}/composer.phar install -o -d #{release_path}"
    end
  end
end

namespace :twig do
  task :cache_folder do
    on roles(:app) do
      execute "mkdir -p #{shared_path}/cache"
      execute "chmod 777 #{shared_path}/cache"
    end
  end
end

namespace :deploy do
  before :publishing, :'composer:setup'
  before :publishing, :'composer:deps'
  before :publishing, :'twig:cache_folder'
end
