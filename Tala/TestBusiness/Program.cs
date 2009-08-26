using System;
using System.Collections.Generic;

using Quantum.Tala.Service.Authentication;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Service.Business;
using System.Xml.Linq;
using System.Threading;
using System.Text;
using System.IO;
using System.Security.Cryptography;
using System.Collections;
using System.Diagnostics;
using Quantum.Tala.Service;
using GURUCORE.Framework.Business;
using Quantum.Tala.Service.DTO;
using GURUCORE.Framework.Core;
using MySql.Data.Types;
using System.Reflection;
//using CH.Combinations;

using System.Linq;
using GURUCORE.Lib.Core.Security.Cryptography;
using Quantum.Tala.Service.VTCGateTopup;
using log4net;

// Load the configuration from the 'WebApp.dll.log4net' file
[assembly: log4net.Config.XmlConfigurator(ConfigFileExtension = "log4net", Watch = true)]


namespace TestBusiness
{
    public class ABCTest : APIDataEntry
    {
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public TalaUser Player { get; set; }
    }


    class T
    {
        public DateTime t{get;set;}
    }

    class Program
    {
        private static readonly ILog log = LogManager.GetLogger(System.Reflection.MethodBase.GetCurrentMethod().DeclaringType);

        static void Main(string[] args)
        {
            TalaProgramApplication.GetInstance().Start(System.IO.Directory.GetCurrentDirectory());
            IAuthenticationService authensvc = ServiceLocator.Locate<IAuthenticationService, AuthenticationService>();

            Console.WriteLine("TEST login:");
            IUser u2 = authensvc.Authenticate("vtc", "vtc21", "111111");
            if (null != u2)
            {
                Console.WriteLine("login OK" + u2.Username);
            }
            else
            {
                Console.WriteLine("login vtc21 failed");
            }

            Console.WriteLine("TEST CheckExisted:");
            Console.WriteLine("exist bkit: " + VTCIntecomService.CheckAccountEXISTS("bkit"));
            Console.WriteLine("exist vtc21: " + VTCIntecomService.CheckAccountEXISTS("vtc21"));
            Console.WriteLine("exist vtc22: " + VTCIntecomService.CheckAccountEXISTS("vtc22"));
            Console.WriteLine("exist vtc23: " + VTCIntecomService.CheckAccountEXISTS("vtc23"));
            Console.WriteLine("exist vtc24: " + VTCIntecomService.CheckAccountEXISTS("vtc24"));
            Console.WriteLine("exist vtc25: " + VTCIntecomService.CheckAccountEXISTS("vtc25"));
            Console.WriteLine("exist vtc26: " + VTCIntecomService.CheckAccountEXISTS("vtc26"));
            Console.WriteLine("exist vtc27: " + VTCIntecomService.CheckAccountEXISTS("vtc27"));
            Console.WriteLine("exist vtc28: " + VTCIntecomService.CheckAccountEXISTS("vtc28"));


            Console.WriteLine("============================================================================");
            Console.WriteLine("TEST GetBalance:");
            Console.WriteLine("balance of bkit: " + VTCIntecomService.GetBalanceOfVTCUser("bkit"));
            Console.WriteLine("balance of vtc21: " + VTCIntecomService.GetBalanceOfVTCUser("vtc21"));
            Console.WriteLine("balance of vtc22: " + VTCIntecomService.GetBalanceOfVTCUser("vtc22"));
            Console.WriteLine("balance of vtc23: " + VTCIntecomService.GetBalanceOfVTCUser("vtc23"));
            Console.WriteLine("balance of vtc24: " + VTCIntecomService.GetBalanceOfVTCUser("vtc24"));
            Console.WriteLine("balance of vtc25: " + VTCIntecomService.GetBalanceOfVTCUser("vtc25"));
            Console.WriteLine("balance of vtc26: " + VTCIntecomService.GetBalanceOfVTCUser("vtc26"));
            Console.WriteLine("balance of vtc27: " + VTCIntecomService.GetBalanceOfVTCUser("vtc27"));
            Console.WriteLine("balance of vtc28: " + VTCIntecomService.GetBalanceOfVTCUser("vtc28"));


            Console.WriteLine("============================================================================");
            Console.WriteLine("TEST Login:");
            CryptoUtil cu = new CryptoUtil();
            Console.WriteLine("login bkit: " + VTCIntecomService.AuthenticateVTC_MD5HashedPassword("bkit", cu.MD5Hash("111111")));


            Console.WriteLine("============================================================================");
            Console.WriteLine("TEST Subtract money BuyItem:");
            Console.WriteLine("BEFORE: balance of bkit: " + VTCIntecomService.GetBalanceOfVTCUser("bkit"));
            int nAccountIDToSubtract = VTCIntecomService.CheckAccountEXISTS("bkit");
            Console.WriteLine("AccountID of bkit: " + nAccountIDToSubtract);

            transactionDTO oExtendOutput;
            Quantum.Tala.Service.VTCBillingService.BuyItemsResponse outputResponse;
            Console.WriteLine("BuyItem bkit: " +
                VTCIntecomService.SubtractVCoinOfVTCUser(nAccountIDToSubtract, "bkit", "TalaITEMCODE", "127.0.0.1", 10000, out oExtendOutput, out outputResponse)
            );
            Console.WriteLine("AFTER: balance of bkit: " + VTCIntecomService.GetBalanceOfVTCUser("bkit"));


            Console.WriteLine("============================================================================");
            Console.WriteLine("TEST Add money TOPUP:");
            Console.WriteLine("BEFORE: balance of bkit: " + VTCIntecomService.GetBalanceOfVTCUser("bkit"));
            nAccountIDToSubtract = VTCIntecomService.CheckAccountEXISTS("bkit");
            Console.WriteLine("AccountID of bkit: " + nAccountIDToSubtract);

            oExtendOutput = null;
            VTCGateResponse oResponse;
            Console.WriteLine("TopUp bkit: " +
                VTCIntecomService.AddVCoinOfVTCUser("bkit", "TalaITEMCODE", "127.0.0.1", 10000, out oResponse)
            );

            Console.WriteLine(string.Format("VTC Response: {0},   {1},   {2},   {3},   {4},   {5},   {6},   {7},   {8},  {9}",
                oResponse.Account, oResponse.Amount, oResponse.Balance,
                oResponse.DataSign, oResponse.Description, oResponse.ExtensionData,
                oResponse.OrgTransId, oResponse.ResponseCode, oResponse.TransDate,
                oResponse.VTCTransCode
                ));

            Console.WriteLine("AFTER: balance of bkit: " + VTCIntecomService.GetBalanceOfVTCUser("bkit"));

            Console.WriteLine("Press enter to quit");
            Console.ReadLine();
        }
        




        static void TestPerformanceXMLStringBuilder()
        {
            #region Test performance with XElement and StringBuilder

            // StringBuilder faster 2x with Xelement, but not signical

            //XElement superX = new XElement("root");
            //StringBuilder superString = new StringBuilder();

            //DateTime start;
            //DateTime end;
            //TimeSpan elap;

            //start = DateTime.Now;
            //for (int i = 0; i < 500000; i++)
            //{
            //    XElement x = new XElement("q",
            //        new XAttribute("a1", "abc"),
            //        new XAttribute("a2", "abc"),
            //        new XAttribute("a3", "abc"),
            //        new XAttribute("a4", "abc"),

            //        new XElement("e1", "abc"),
            //        new XElement("e2", "abc"),
            //        new XElement("e3", "abc"),
            //        new XElement("e4", "abc"),
            //        new XElement("e5", "abc"),
            //        new XElement("e",
            //            new XElement("ec1", "abc"),
            //            new XElement("ec2", "abc"),
            //            new XElement("ec3", "abc"),
            //            new XElement("ec4", "abc")
            //            )
            //        );
            //    superX.Add(x);//x.ToString();
            //}

            //end = DateTime.Now;
            //elap = end.Subtract(start);
            //Console.WriteLine(elap.TotalMilliseconds);

            //start = DateTime.Now;
            ////superX.ToString();
            //end = DateTime.Now;
            //elap = end.Subtract(start);
            //Console.WriteLine(elap.TotalMilliseconds);


            //Thread.Sleep(1000);



            //start = DateTime.Now;
            //for (int i = 0; i < 500000; i++)
            //{
            //    string s1 = "<e1>abc</e1><e1>abc</e1><e1>abc</e1><e1>abc</e1><e1>abc</e1>";
            //    string s2 = "<e><ec1>abc</ec1><ec1>abc</ec1><ec1>abc</ec1><ec1>abc</ec1><e>";
            //    string s = string.Format(
            //        "<q a1='{2}' a2='{3}' a3='{4}' a4='{5}' >{0}{1}</q>",
            //        s1, s2, "abc", "abc", "abc", "abc"
            //        );
            //    superString.Append(s);
            //}
            //superString.ToString();
            //end = DateTime.Now;
            //elap = end.Subtract(start);
            //Console.WriteLine(elap.TotalMilliseconds);

            #endregion
        }

        public static string EncryptString(string inputString, int dwKeySize,
                             string xmlString)
        {
            // TODO: Add Proper Exception Handlers
            RSACryptoServiceProvider rsaCryptoServiceProvider = new RSACryptoServiceProvider(dwKeySize);
            rsaCryptoServiceProvider.FromXmlString(xmlString);
            int keySize = dwKeySize / 8;
            byte[] bytes = Encoding.UTF32.GetBytes(inputString);
            // The hash function in use by the .NET RSACryptoServiceProvider here 
            // is SHA1
            // int maxLength = ( keySize ) - 2 - 
            //              ( 2 * SHA1.Create().ComputeHash( rawBytes ).Length );
            int maxLength = keySize - 42;
            int dataLength = bytes.Length;
            int iterations = dataLength / maxLength;
            StringBuilder stringBuilder = new StringBuilder();
            for (int i = 0; i <= iterations; i++)
            {
                byte[] tempBytes = new byte[
                        (dataLength - maxLength * i > maxLength) ? maxLength :
                                                      dataLength - maxLength * i];
                Buffer.BlockCopy(bytes, maxLength * i, tempBytes, 0,
                                  tempBytes.Length);
                byte[] encryptedBytes = rsaCryptoServiceProvider.Encrypt(tempBytes,
                                                                          false);
                // Be aware the RSACryptoServiceProvider reverses the order of 
                // encrypted bytes. It does this after encryption and before 
                // decryption. If you do not require compatibility with Microsoft 
                // Cryptographic API (CAPI) and/or other vendors. Comment out the 
                // next line and the corresponding one in the DecryptString function.
                Array.Reverse(encryptedBytes);
                // Why convert to base 64?
                // Because it is the largest power-of-two base printable using only 
                // ASCII characters
                stringBuilder.Append(Convert.ToBase64String(encryptedBytes));
            }
            return stringBuilder.ToString();
        }

        public static string DecryptString(string inputString, int dwKeySize,
                                     string xmlString)
        {
            // TODO: Add Proper Exception Handlers
            RSACryptoServiceProvider rsaCryptoServiceProvider
                                     = new RSACryptoServiceProvider(dwKeySize);
            rsaCryptoServiceProvider.FromXmlString(xmlString);
            int base64BlockSize = ((dwKeySize / 8) % 3 != 0) ?
              (((dwKeySize / 8) / 3) * 4) + 4 : ((dwKeySize / 8) / 3) * 4;
            int iterations = inputString.Length / base64BlockSize;
            ArrayList arrayList = new ArrayList();
            for (int i = 0; i < iterations; i++)
            {
                byte[] encryptedBytes = Convert.FromBase64String(
                     inputString.Substring(base64BlockSize * i, base64BlockSize));
                // Be aware the RSACryptoServiceProvider reverses the order of 
                // encrypted bytes after encryption and before decryption.
                // If you do not require compatibility with Microsoft Cryptographic 
                // API (CAPI) and/or other vendors.
                // Comment out the next line and the corresponding one in the 
                // EncryptString function.
                Array.Reverse(encryptedBytes);
                arrayList.AddRange(rsaCryptoServiceProvider.Decrypt(
                                    encryptedBytes, false));
            }
            return Encoding.UTF32.GetString(arrayList.ToArray(
                                      Type.GetType("System.Byte")) as byte[]);
        }









        static void TestU()
        {
            List<Card> cardList = new List<Card>();

            //Card card = new Card("02", "c");
            //cardList.Add(card);
            //card = new Card("02", "d");
            //cardList.Add(card);
            //card = new Card("02", "p");
            //cardList.Add(card);
            //card = new Card("02", "t");
            //cardList.Add(card);
            //card = new Card("03", "d");
            //cardList.Add(card);
            //card = new Card("04", "d");
            //cardList.Add(card);
            //card = new Card("05", "d");
            //cardList.Add(card);
            //card = new Card("06", "d");
            //cardList.Add(card);
            //card = new Card("05", "c");
            //cardList.Add(card);
            //card = new Card("05", "p");
            //cardList.Add(card);

            DateTime start;
            DateTime end;
            TimeSpan elap;

            start = DateTime.Now;
            Card card = new Card("01", "t");
            cardList.Add(card);
            card = new Card("02", "c");
            cardList.Add(card);
            //card = new Card("02", "d");
            //cardList.Add(card);
            //card = new Card("02", "p");
            //cardList.Add(card);
            //card = new Card("02", "t");
            //cardList.Add(card);
            card = new Card("03", "t");
            cardList.Add(card);
            card = new Card("04", "t");
            cardList.Add(card);
            card = new Card("05", "c");
            cardList.Add(card);
            card = new Card("05", "d");
            cardList.Add(card);
            card = new Card("05", "t");
            cardList.Add(card);

            List<Card> baiDaAn = new List<Card>();
            baiDaAn.Add(new Card("02", "t"));
            baiDaAn.Add(new Card("02", "p"));
            baiDaAn.Add(new Card("02", "d"));

            int count = UUtil.CheckU(cardList, baiDaAn);
            end = DateTime.Now;
            elap = end.Subtract(start);
            System.Console.Out.WriteLine("========== U result: " + count + "====== elapse: " + elap.TotalMilliseconds);

            Console.ReadLine();
        }

        // static int uCount = 0;

        static void TestMain(string[] args)
        {
            //Card[] input = Card.CARD_SET;
            //Combinations<Card> combinations = new Combinations<Card>(input, 10);
            //int i = 0;
            //FileStream fs = new FileStream("C:/u.txt", FileMode.Create, FileAccess.Write);
            //StreamWriter sw = new StreamWriter(fs);
            //sw.WriteLine("Combination lists: ");
            //foreach (Card[] combination in combinations)
            //{
            //    MakeCardListAndCheckU(combination, sw);
            //    i++;
            //}
            //sw.WriteLine("=====================");
            //sw.WriteLine("Total Combination count: " + i);
            //sw.WriteLine("Total U Combination count: " + uCount);

            //sw.Close();
            //fs.Close();
            //Console.WriteLine("DONE!!!");
            //Console.ReadLine();

        }

        //static void MakeCardListAndCheckU(Card[] cardArr, StreamWriter sw)
        //{
        //    List<Card> cardList = new List<Card>(10);
        //    for (int i = 0; i < cardArr.Length; i++)
        //    {
        //        cardList.Add(cardArr[i]);
        //    }
        //    int count = UUtil.CheckU(cardList);
        //    if (count >= 9)
        //    {
        //        sw.WriteLine("Combination " + uCount + ": Count = " + count);
        //        WriteCombination(cardArr, sw);
        //        uCount++;
        //    }
        //}

        //static void WriteCombination(Card[] combination, StreamWriter sw)
        //{
        //    for (int i = 0; i < combination.Length; i++)
        //    {
        //        Card card = combination[i];
        //        sw.Write(card.ToString() + " ");
        //    }
        //    sw.WriteLine("");
        //    sw.WriteLine("=======================");
        //}

    }
}