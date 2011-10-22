
 Utility used to plot maintenance run times to see each task ran during Maintenance and how long they take over time.

 0) Requirements
- gnuplot
- Python
- OpenX debug.log should be enabled and log at least INFO messages. Your openx/var/domain.conf.php should look like:
	[log]
	enabled=true
	methodNames=false
	lineNumbers=false
	type=file
	name="debug.log"
	priority=PEAR_LOG_INFO
- Make both files executable
$ chmod 777 plot extract

 1) Prepare the logs
cat debug.log | egrep "Maintenance|complete" | egrep "info]" > debug-to-plot.log
You can edit "extract" and set the log file name that you want to plot
 
 2) Check that log parsing works
./extract
this should just output values without printing error messages

 3) Plot the graph
./plot

This will generate the times.png image