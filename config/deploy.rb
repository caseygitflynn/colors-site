require "s3"
require "mimemagic"

# config valid only for current version of Capistrano
lock '3.4.0'

set :application, 'colors'
set :repo_url, 'git@github.com:nightagency/kiehls-zoolander.git'

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
set :linked_files, %w{.env public/robots.txt}

# Default value for linked_dirs is []
set :linked_dirs, fetch(:linked_dirs, []).push('cache')

# Default value for default_env is {}
# set :default_env, { path: "/opt/ruby/bin:$PATH" }

# Default value for keep_releases is 5
# set :keep_releases, 5

set :kiehls_deployment_id, DateTime.now.to_time.to_i.to_s

namespace :s3 do
  desc "Upload stuff to S3"
  task :upload do
    deployment = fetch(:kiehls_deployment_id)

    service = S3::Service.new(
      :access_key_id     => "AKIAJLJKEU4JPQC7BSGQ",
      :secret_access_key => "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"
    )

    bucket = service.buckets.find("kiehls1851")

    files  = Dir["public/css/*.css"].to_a
    files += Dir["public/js/*.js"].to_a
    files += Dir["public/img/**/*.*"].to_a
    files += Dir["public/webfonts/*.*"].to_a
    files += Dir["public/video/**/*.*"].to_a
    files += Dir["public/audio/**/*.*"].to_a

    files.each do |source|
      remote_path = 'kiehls-zoolander' + '/' + deployment + '/' + source.sub(/public\//, '')
      object = bucket.objects.build(remote_path)
      object.content = File.open(source)
      object.content_type = MimeMagic.by_path(source)
      object.cache_control = "max-age=432000"
      object.acl = :public_read
      object.save
      puts "Uploaded https://#{bucket.name}.s3.amazonaws.com/#{remote_path}"
    end
  end
end

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

namespace :assets do
  task :build do
    sh 'gulp --production'
  end

  task :upload => [:build, :'s3:upload'] do
    on roles(:web) do
      deployment = fetch(:kiehls_deployment_id)
      upload! StringIO.new(deployment), "#{release_path}/public/deployment.txt", {mode: 0755}
    end
  end
end

namespace :deploy do
  before :publishing, :'composer:setup'
  before :publishing, :'composer:deps'
  before :publishing, :'twig:cache_folder'
  before :publishing, :'assets:upload'
end
# config valid only for current version of Capistrano
lock '3.4.0'

set :application, 'my_app_name'
set :repo_url, 'git@example.com:me/my_repo.git'

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
# set :linked_files, fetch(:linked_files, []).push('config/database.yml', 'config/secrets.yml')

# Default value for linked_dirs is []
# set :linked_dirs, fetch(:linked_dirs, []).push('log', 'tmp/pids', 'tmp/cache', 'tmp/sockets', 'vendor/bundle', 'public/system')

# Default value for default_env is {}
# set :default_env, { path: "/opt/ruby/bin:$PATH" }

# Default value for keep_releases is 5
# set :keep_releases, 5

namespace :deploy do

  after :restart, :clear_cache do
    on roles(:web), in: :groups, limit: 3, wait: 10 do
      # Here we can do anything such as:
      # within release_path do
      #   execute :rake, 'cache:clear'
      # end
    end
  end

end
