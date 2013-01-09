base_path='/var/www/app'
cd $base_path/scripts
logfile="$base_path/scripts/log/indexer.log"
sites=(snapdeal koovs mydala scoopstr taggle dealivore dealsandyou)

for site in ${sites[@]}
do
    ./cron.php --log-file=$logfile --run=/indexer/index/$site
done
