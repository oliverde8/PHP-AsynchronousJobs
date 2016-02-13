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

Once you started some jobs you may decide you need to wait for them to finish. 

So once you have started your jobs :
```
$job1->run()
$job2->run()
// ....
```

You can use wait all : 
```
JobRunner::getInstance()->waitForAll(1);
```
This will block you instance until all jobs habe finished their execution. waitForAll takes in parameters the sleep time. So if you know your jobs takes a few jours to run you can increase the sleep time to a few minutes to prevent IO to be over used.

**TODO** Have a $job->wait to wait only a single job?

### Having callbacks
**TODO**

### Custom settings
Yo have custom settings you must call 
```
JobRunner::getInstance()
```
Before running initiating any jobs before, it needs to be your first call !!

The methods can take the fallowing parameters : 
* **$id** Allows you to share instances between multiple process, leave it null if you don't understand it should rarely need changing. 
* **$phpExecutable** Path to the php executable, can be an issue on windows, on linux php alone should suffice. 
* **$tmpPath** Path to put the temporary files used to synchronize the process. It needs to be writable by the process.

## Todo 
* **Some traits :** To add callback functionality on jobs easily

I hope to finish all this by the 21/02/2016

## In the future
* Redis support (so much cooler & faster)
* Semaphore support
* Shared memory between jobs
