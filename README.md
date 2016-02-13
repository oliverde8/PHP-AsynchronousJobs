# Asynchronous - Jobs

**WORK IN PROGRESS**

PHP AsynchronousJobs is a small library allowing to run PHP code in parallel of your main code. In some ways it works like threads but it doesen't use the proper system libraries to do so. 

The library was created to work on windows & linux systems without the need of installing phpthread extension. **The library must not be considered as replacement to threads !!** The way the library works isn't optimized to for that. 

It is built in the purpose of doing long tasks in parallel. A good example would be to download filess while the main script is serving informations to users. The library is ment to be used for command line tools & deamons. 

## Usage 

First you need to create a job : 
```
class Sleep extends Job
{
    public $time = 1;

    /**
     * Method called by the new instance to run the job.
     *
     * @return mixed
     */
    public function run()
    {
        sleep($this->time);
    }

    /**
     * Method called by the original instance when the job has ran.
     *
     * @return mixed
     */
    public function end()
    {
      $time = $this->time;
      echo "I end after : $time!";
    }
}
```

Then you can create a job : 
```
$job1 = new Sleep();
$job1->sleep = 3;
```

Let's create a second job as well.
```
$job2 = new Sleep();
$job2->sleep = 2;
```

Now execute the jobs
```
$job1->run()
$job2->run()

// And wait for the end
sleep (4);
```

You should see first "I end after 2" then the message "I end after 3"

### Waiting for ongoing jobs 
**TODO**

### Having callbacks
**TODO**

### Custom settings
**TODO**

## Todo 
* **Process ongoing :** Need to checkall the on going jobs every X second & add the pending tasks as need be
* **Clean files:** At the moment the code is in PIG mode, it leaves everything behind
* **Configuration - PHP Binary :** At the moment will work on very few windows installations.
* **Configuration - Tmp Path :** Need to setup the path to use any
* **Some basic jobs :** For example a curl job that will download stuff (main usage for me)
* **Some traits :** To add callback functionality on jobs easily

I hope to finish all this by the 21/02/2016

## In the future
* Redis support (so much cooler & faster)
* Semaphore support
* Shared memory between jobs
