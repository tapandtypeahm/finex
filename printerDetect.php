<?php
                    require_once('lib/PrintIPP.php');
                    
                    $ipp = new PrintIPP();
                    
                    $ipp->setHost("localhost");
                    $ipp->setPrinterURI("/printers/epson");
                    $ipp->setData("jeet"); // Path to file.
                    $ipp->printJob();

                ?>
                    
