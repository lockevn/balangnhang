using System;
using Microsoft.Win32;
using System.Collections;
using System.Collections.Generic;
using System.Collections.Specialized;

using System.Globalization;
using System.IO;
using System.Xml;

namespace GURUCORE.GForm.CodeGenerator
{	
	public class GlobalOptions
	{
		private static object m_oLock = new object();
		private static GlobalOptions m_oInstance;
        private HybridDictionary m_htProfile = new HybridDictionary();
        private string m_sCurrentProfileName;
        public string CurrentProfileName
        {
            get { return m_sCurrentProfileName; }
            set { m_sCurrentProfileName = value;
            m_sCurrentProfile = (ProfileGO)m_htProfile[value];
            }
        }

        private ProfileGO m_sCurrentProfile;
        public ProfileGO CurrentProfile
        {
            get { return m_sCurrentProfile; }
            set { m_sCurrentProfile = value;
            m_sCurrentProfileName = value.Name;
        }
        }


		private GlobalOptions()
		{
		}

        [System.Diagnostics.DebuggerStepThrough]
		public static GlobalOptions GetInstance()
		{
			lock (m_oLock)
			{
				if (m_oInstance == null)
				{
					m_oInstance = new GlobalOptions();
				}
				return m_oInstance;
			}
		}



        /// <summary>
        /// get list of predefined profiles
        /// </summary>
        /// <returns></returns>
        public StringCollection GetProfileList()
        {
            StringCollection arr = new StringCollection();
            foreach (string profileName in m_htProfile.Keys)
            {
                arr.Add(profileName);
            }
            return arr;            
        }

        /// <summary>
        /// Get profile by name. Profile is save in a file-base repository.
        /// </summary>
        /// <param name="p_sProfileName">What profile</param>
        /// <returns>the profile information, empty profile if null</returns>
        public ProfileGO GetProfile(string p_sProfileName)
        {
            if (m_htProfile.Contains(p_sProfileName))
            {
                return (ProfileGO)m_htProfile[p_sProfileName];
            }
            else
            {
                return new ProfileGO();
            }
        }

        

        public void ReadFileRepository()
        {
            // read the XML file, return the XMLNode of profile
            XmlReaderSettings XRS = new XmlReaderSettings();
            XRS.CloseInput = true;
            XRS.IgnoreComments = true;
            XRS.IgnoreProcessingInstructions = true;
            XmlReader xReader = XmlReader.Create("Profiles.xml", XRS);
            XmlDocument xDoc = new XmlDocument();
            try
            {
                xDoc.Load(xReader);
            }
            catch //(Exception ex)
            {
                xReader.Close();
            }

            XmlElement xRoot = xDoc.DocumentElement;
            XmlNodeList arrNode = xRoot.SelectNodes("/profiles/profile");

            foreach (XmlNode xnode in arrNode)
            {
                // for each XML Node
                // add the profile to the m_htProfile
                ProfileGO profile = Convert(xnode);
                if (m_htProfile.Contains(profile.Name))
                {
                    m_htProfile[profile.Name] = profile;
                }                    
                else
                {
                    m_htProfile.Add(profile.Name, profile);
                }
                // m_sCurrentProfile = profile;
                m_sCurrentProfileName = profile.Name;
            }
            xReader.Close();            
        }

        public void WriteFileRepository()
		{
            

            XmlWriterSettings XWS = new XmlWriterSettings();
            XWS.Indent = true;
            try
            {
                File.Copy("Profiles.xml", "Profiles.xml.bak", true);
            }
            catch{}

            XmlWriter xWriter = null;
            XmlDocument xDoc = new XmlDocument();

            try
            {
                File.Delete("Profiles.xml");
                xWriter = XmlWriter.Create("Profiles.xml", XWS);                
                XmlNode xNodeProfiles = xDoc.CreateNode(XmlNodeType.Element, "profiles", string.Empty);
                xDoc.AppendChild(xNodeProfiles);                

                foreach (ProfileGO profile in m_htProfile.Values)
                {
                    XmlElement xprofile = xDoc.CreateElement("profile");
                    xprofile.SetAttribute("name", profile.Name);

                    XmlElement xNode = xDoc.CreateElement("TablePrefixes");
                    xNode.InnerText = profile.TablePrefixes;                     xprofile.AppendChild(xNode);
                    xNode = xDoc.CreateElement("DefaultNameSpace");
                    xNode.InnerText = profile.DefaultNameSpace;                     xprofile.AppendChild(xNode);
                    xNode = xDoc.CreateElement("SaveFolder");
                    xNode.InnerText = profile.SaveFolder;                     xprofile.AppendChild(xNode);
                    xNode = xDoc.CreateElement("ServerName");
                    xNode.InnerText = profile.ServerName;                     xprofile.AppendChild(xNode);
                    xNode = xDoc.CreateElement("Database");
                    xNode.InnerText = profile.Database;                     xprofile.AppendChild(xNode);
                    xNode = xDoc.CreateElement("User");
                    xNode.InnerText = profile.User;                     xprofile.AppendChild(xNode);
                    xNode = xDoc.CreateElement("Password");
                    xNode.InnerText = profile.Password;                     xprofile.AppendChild(xNode);

                    xNodeProfiles.AppendChild(xprofile);
                }

                xDoc.Save(xWriter);
            }
            catch // (Exception ex)
            {
            }
            finally
            {
                if(null != xWriter)
                {
                    xWriter.Close();
                }
                try
                {
                    File.Delete("Profiles.xml.bak");
                }
                catch { }
            }

		}


        private ProfileGO Convert(XmlNode p_o)
        {
            ProfileGO profile = new ProfileGO();
            profile.Name = p_o.Attributes["name"].InnerText;
            profile.TablePrefixes = p_o["TablePrefixes"].InnerText;
            profile.DefaultNameSpace = p_o["DefaultNameSpace"].InnerText;
            profile.SaveFolder = p_o["SaveFolder"].InnerText;
            profile.ServerName = p_o["ServerName"].InnerText;
            profile.Database = p_o["Database"].InnerText;
            profile.User = p_o["User"].InnerText;
            profile.Password = p_o["Password"].InnerText;

            return profile;
        }       


        #region OBSOLETE Read Single profile from Registry


        //public void ReadFromRegistry()
        //{
        //    RegistryKey oBaseKey = Registry.LocalMachine;
        //    RegistryKey oSubKey = oBaseKey.CreateSubKey("SOFTWARE\\GURUCORE\\Portal\\CodeGenerator");
        //    try
        //    {
        //        m_sTablePrefixes = oSubKey.GetValue("TablePrefixes").ToString();
        //        m_sDefaultNameSpace = oSubKey.GetValue("DefaultNameSpace").ToString();
        //        m_sSaveFolder = oSubKey.GetValue("SaveFolder").ToString();

        //        m_sServerName = oSubKey.GetValue("ServerName").ToString();
        //        m_sDatabase = oSubKey.GetValue("Database").ToString();
        //        m_sUser = oSubKey.GetValue("User").ToString();
        //        m_sPassword = oSubKey.GetValue("Password").ToString();
        //    }
        //    catch {}
        //}

        //public void WriteToRegistry()
        //{
        //    RegistryKey oBaseKey = Registry.LocalMachine;
        //    RegistryKey oSubKey = oBaseKey.CreateSubKey("SOFTWARE\\AlphanamICT\\WAF\\CodeGenerator");
        //    oSubKey.SetValue("TablePrefixes",m_sTablePrefixes);
        //    oSubKey.SetValue("DefaultNameSpace",m_sDefaultNameSpace);
        //    oSubKey.SetValue("SaveFolder",m_sSaveFolder);

        //    oSubKey.SetValue("ServerName",m_sServerName);
        //    oSubKey.SetValue("Database",m_sDatabase);
        //    oSubKey.SetValue("User",m_sUser);
        //    oSubKey.SetValue("Password",m_sPassword);
        //}

        #endregion

	}
}
