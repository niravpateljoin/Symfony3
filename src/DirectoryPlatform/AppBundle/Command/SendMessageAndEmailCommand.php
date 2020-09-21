<?php

namespace DirectoryPlatform\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
// use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * php bin/console send-message-and-email
 * Class SendNewslaterEmailToSubscriberCommand
 *
 * @package AppBundle\Command
 */

class SendMessageAndEmailCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('send-message-and-email')
            ->setDescription('Send an specific newslater to all the subscriber on sent date of news later');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln( "\n####### Started Send Message and Email cron at " . date( 'M j H:i' ) . " #######" );
        $em = $this->getContainer()->get( 'doctrine' )->getManager();
        $objProducts = $this->getAllProductDetails();
        $getEmailTemplate = $this->getEmailTemplateData();
        $output->writeln( "####### Total Found Product:  '" . count( $objProducts ) . "'  #######" );
        foreach($objProducts as $objProduct){
            $objUserDataInRadius = $this->getDataFromRadius($objProduct);
            
            if(count($objUserDataInRadius) > 0){

                foreach ($objUserDataInRadius as $objUserData) {

                    $smsSend = $this->sendMessageService($objUserData, $objProduct);
                    
                    if($smsSend == 'success'){
                        $output->writeln("####### Message sent to:  '".$objUserData[0]->getPhone()."'  #######");
                    }else{
                        $output->writeln("####### Message sending failed to:  '".$objUserData[0]->getPhone()."'  #######");
                    }

                    $arrSearch = array();
                    $arrReplace = array();

                    $parameters = array(
                        '##UserFirstName##'        => $objUserData[0]->getDisplayName(),
                        '##ProductName##'          => $objProduct->getName(),
                        '##ProductDescription##'   => $objProduct->getDescription(),
                        '##OwnerName##'            => $objProduct->getUser()->getDisplayName(),
                        '##ConatctNumber##'         => $objProduct->getUser()->getPhone()
                    );

                    $to_email   = $objUserData[0]->getUsername();
                    $from_email = $this->getContainer()->getParameter('app.email_from');

                    foreach($parameters as $key => $value){
                        $arrSearch[] = $key;
                        $arrReplace[] = $value;
                    }

                    $bodyHtml = str_replace($arrSearch, $arrReplace, $getEmailTemplate->getBody());
                    $subject = str_replace($arrSearch, $arrReplace, $getEmailTemplate->getSubject());

                    try {
                        $message = \Swift_Message::newInstance()
                            ->setSubject($subject)
                            ->setFrom($from_email)
                            ->setTo($to_email)
                            ->setBody($bodyHtml, 'text/html')
                        ;

                        $this->getContainer()->get('mailer')->send($message);
                        $output->writeln("####### Email sent to:  '".$objUserData[0]->getUsername()."'  #######");

                    } catch (\Exception $ex){
                        $output->writeln("####### Email sending failed to:  '".$objUserData[0]->getUsername()."'  #######");
                    }
                }
            }
        }
        
        // $output->writeln( "####### Email sent to: " . $i . " Registered Users #######" );
        $output->writeln( "####### Finished Send Message and Email queue cron at " . date( 'M j H:i' ) . " #######\n" );
    }

    private function getDataFromRadius($objProduct)
    {
        $lat = $objProduct->getLatitude();
        $long = $objProduct->getLongitude();
        $distance = $this->getRediusData();
        $objItems = $this->getContainer()->get( 'doctrine' )->getRepository( "AppBundle:User" )
                         ->createQueryBuilder( "User" )
                         ->addSelect('((ACOS(SIN(:lat * (22/7) / 180) * SIN(User.latitude * (22/7) / 180) + COS(:lat * (22/7) / 180) * COS(User.latitude * (22/7) / 180) * COS((:lng - User.longitude) * (22/7) / 180)) * 180 / (22/7)) * 60 * 1.1515) as distance')
                         ->setParameter('lat', $lat)
                         ->setParameter('lng', $long)
                         ->having( 'distance < :distance' )
                         ->setParameter(':distance', $distance)
                         ->getQuery()
                         ->getResult();
        return  $objItems;
    }
    
    private function getAllProductDetails(){
        $objItems2 = $this->getContainer()->get( 'doctrine' )->getRepository( "AppBundle:Listing" )
                         ->createQueryBuilder( "listing")
                         ->andWhere('listing.isPublished = 1')
                         ->getQuery()
                         ->getResult();

        return $objItems2;
    }

    private function getEmailTemplateData()
    {
        $emailTemplate = $this->getContainer()->get( 'doctrine' )->getRepository('AppBundle:EmailTemplate')->findOneBy(array("emailKey" => 'SEND_EMAIL_CRON'));

        return $emailTemplate;

    }

    public function sendMessageService($objUser, $objProduct){

        $username = "nilupatel846@gmail.com";
        $hash = "c1e601c24e734216600b2f8d5189eaf10cc9e87f2541dc0f19273efbfd1456f9";

        // Config variables. Consult http://api.textlocal.in/docs for more info.
        $test = "0";

        // Data for text message. This is the text message data.
        $sender = "TXTLCL"; // This is who the message appears to be from.
        $numbers = $objUser[0]->getPhone(); // A single number or a comma-seperated list of numbers
        $message = "Hello ".$objUser[0]->getUsername().", Product Details - Name : ".$objProduct->getName()." - Description : ".str_replace("&nbsp;","",strip_tags($objProduct->getDescription()))." - Product Owner : ".$objProduct->getUser()->getDisplayName()." - Phone Number : ".$objProduct->getUser()->getPhone();      
        // 612 chars or less
        // A single number or a comma-seperated list of numbers
        $message = urlencode($message);
        $data = "username=".$username."&hash=".$hash."&message=".$message."&sender=".$sender."&numbers=".$numbers."&test=".$test;
        $ch = curl_init('http://api.textlocal.in/send/?');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch); // This is the result from the API
        curl_close($ch);

        $response = json_decode($result, true);
        return $response['status'];
    }

    public function getRediusData(){
        $defaultShowProduct = $this->getContainer()->get( 'doctrine' )->getRepository('AppBundle:Setting')->findOneBy(array("keyVal" => 'RADIUS'));

        return $defaultShowProduct->getValue();

    }
}
?>