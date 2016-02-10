set :stage, :development
set :deploy_to, -> { "/home1/flynnani/public_html/#{fetch(:application)}" }
set :tmp_dir, "/home1/flynnani/capistrano_tmp"

server 'flynnanigans.com', user: 'flynnani', roles: %w{web app}

# you can set custom ssh options
# it's possible to pass any option but you need to keep in mind that net/ssh understand limited list of options
# you can see them in [net/ssh documentation](http://net-ssh.github.io/net-ssh/classes/Net/SSH.html#method-c-start)
# set it globally
set :ssh_options, {
    forward_agent: true
}
